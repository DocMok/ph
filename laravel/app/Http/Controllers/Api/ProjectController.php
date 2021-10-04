<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetProjectsRequest;
use App\Http\Requests\Api\LikedProjectsRequest;
use App\Http\Requests\Api\ProjectStoreRequest;
use App\Http\Requests\Api\UpdateProjectRequest;
use App\Http\Requests\Api\GetProjectRequest;
use App\Http\Requests\Api\ProjectLikeRequest;
use App\Http\Resources\OneProjectResource;
use App\Http\Resources\ProjectResource;
use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponsable;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use ApiResponsable;

    const MAX_ITEMS_PER_PAGE = 20;

    /**
     * @OA\Get(
     *     path="/api/projects",
     *     description="Get projects with/without filters",
     *     tags={"Projects"},
     *     @OA\Parameter(name="category_ids",description="Array of category ids",required=false,in="query",@OA\Schema(type="json")),
     *     @OA\Parameter(name="currency",description="Currency",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="min",description="Min amount to invest",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="max",description="Max amount to invest",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit",description="Projects per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/projects.list.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="projects.list.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",
     *      @OA\Property(property="pages_total", type="integer", example=3),
     *      @OA\Property(property="projects", type="array",
     *          @OA\Items(ref="#/components/schemas/project.response"),
     *      ),
     *   ),
     * )
     */
    public function index(GetProjectsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $projectsQuery = Project::filter($request, $user);

        $projectsTotal = $projectsQuery->count();
        $projects = $projectsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($projectsTotal / $limit),
            'projects' => ProjectResource::collection($projects),
        ];

        return $this->successResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/project",
     *     description="Get project info",
     *     tags={"Projects"},
     *     @OA\Parameter(name="id",description="Project id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/one.project.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function show(GetProjectRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $project = Project::find($request->id);

        return $this->successResponse(new OneProjectResource($project));
    }

    /**
     * @OA\Post(
     *     path="/api/project",
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
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/store.update.project.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="store.update.project.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="string", example="ok"),
     *   )
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
     *     path="/api/project/update",
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
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/store.update.project.response")),
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

    /**
     * @OA\Post(
     *     path="/api/project/like-toggle",
     *     description="Like project",
     *     tags={"Projects"},
     *     @OA\Parameter(name="id",description="Project id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/like.toggle.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="like.toggle.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",
     *      @OA\Property(property="likes_total",type="integer",example=3),
     *   ),
     *   )
     */
    public function likeToggle(ProjectLikeRequest $request, NotificationService $NS)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $project = Project::find($request->id);
        $toggleResult = $project->likes()->toggle($user->id);

        if (!empty($toggleResult['attached'])) {
            $title = 'PartnerHub notification';
            $body = 'Somebody liked your project ' . $project->name . ' recently';

            $NS->notificate($user, $project->projectOwner->user, $project, $title, $body, 'Liked your project');
        }

        $response = ['likes_total' => $project->likes()->count()];

        return $this->successResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/projects/liked",
     *     description="Liked projects",
     *     tags={"Projects"},
     *     @OA\Parameter(name="limit",description="Projects per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/projects.list.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function liked(LikedProjectsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $projectsQuery = $user->likedProjects();
        $projectsTotal = $projectsQuery->count();
        $projects = $projectsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($projectsTotal / $limit),
            'projects' => ProjectResource::collection($projects),
        ];
        return $this->successResponse($response);
    }
}
