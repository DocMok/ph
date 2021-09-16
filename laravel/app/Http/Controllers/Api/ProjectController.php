<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProjectStoreRequest;
use App\Http\Requests\Api\UpdateProjectRequest;
use App\Http\Traits\ApiResponsable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Post(
     *     path="/api/projects",
     *     description="Create project",
     *     tags={"Projects"},
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                   @OA\Property(description="Project logo",property="logo",type="file", format="binary")
     *              )
     *         )
     *     ),
     *     @OA\Parameter(name="name",description="Project name",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="description",description="Project description",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id",description="Category id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="currency",description="Currency",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="amount_available",description="Amount available money",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="amount_remaining",description="Amount remaining money",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.profile.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function store(ProjectStoreRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        if ($user->user_type == User::INVESTOR) {
            return $this->errorResponse('Wrong user type');
        }

        $project = Project::create([
            'project_owner_id' => $user->typeable->id,
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'currency' => $request->currency,
            'amount_available' => $request->amount_available,
            'amount_remaining' => $request->amount_remaining,
        ]);

        if ($request->file('logo')) {
            $logoPath = $request->file('logo')->store("projects/logos/{$project->id}", ['disk' => 'public']);
            $project->logo = $logoPath;
            $project->save();
        }

        return $this->successResponse('ok');
    }


    /**
     * @OA\Post(
     *     path="/api/projects/update",
     *     description="Update project",
     *     tags={"Projects"},
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                   @OA\Property(description="Project logo",property="logo",type="file", format="binary")
     *              )
     *         )
     *     ),
     *     @OA\Parameter(name="id",description="Project id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="name",description="Project name",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="description",description="Project description",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id",description="Category id",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="currency",description="Currency",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="amount_available",description="Amount available money",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="amount_remaining",description="Amount remaining money",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.profile.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function update(UpdateProjectRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }
        if ($user->user_type == User::INVESTOR) {
            return $this->errorResponse('Wrong user type');
        }

        $project = Project::find($request->id);

        if ($project->project_owner_id != $user->typeable->id) {
            return $this->errorResponse('You\'re not the owner of this project');
        }

        $project->update($request->except('id', 'logo'));

        if ($request->file('logo')) {
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $logoPath = $request->file('logo')->store("projects/logos/{$project->id}", ['disk' => 'public']);
            $project->logo = $logoPath;
            $project->save();
        }


        return $this->successResponse('ok');
    }
}
