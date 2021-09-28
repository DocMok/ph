<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetUserProfileRequest;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Http\Resources\UserProfileResource;
use App\Http\Traits\ApiResponsable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     description="Get user profile. !!!ATTENTION!!! Response may has different structure (see in schemas: project.owner.profile.response, investor.profile.response)",
     *     tags={"User profile"},
     *     @OA\Parameter(name="id",description="User id",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.profile.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="user.profile.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",oneOf={
     *      @OA\Schema(ref="#/components/schemas/project.owner.profile.response"),
     *      @OA\Schema(ref="#/components/schemas/investor.profile.response")},
     *   ),
     * )
     */
    public function getProfile(GetUserProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }
        !$request->id ?: $user = User::find($request->id);
        return $this->successResponse(new UserProfileResource($user));
    }


    /**
     * @OA\Post (
     *     path="/api/user/profile",
     *     description="Update user profile",
     *     tags={"User profile"},
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                   @OA\Property(description="User photo",property="photo",type="file", format="binary")
     *              )
     *         )
     *     ),
     *     @OA\Parameter(name="name",description="User name",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="email",description="Unique user email",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="job",description="User's job description",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="phone",description="User's phone number",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="amount",description="Investor's balance. Required when user_type=Investor",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="currency",description="Currency. Required when user_type=Investor",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="category_ids",description="Investor's prefered category ids. Required when user_type=Investor",required=false,in="query",@OA\Schema(type="array", @OA\Items(type="integer"))),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.profile.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        if ($request->file('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $photoPath = $request->file('photo')->store("user/photos/{$user->id}", ['disk' => 'public']);
            $user->photo = $photoPath;
            $user->save();
        }

        $user->update($request->except('amount', 'currency', 'category_ids', 'photo'));
        if ($user->user_type == User::INVESTOR) {
            $user->typeable->update($request->only('amount', 'currency'));
            $user->typeable->categories()->sync($request->category_ids);
        }
        return $this->successResponse(new UserProfileResource($user));
    }

}
