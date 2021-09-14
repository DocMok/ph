<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Http\Resources\UserProfileResource;
use App\Http\Traits\ApiResponsable;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Post(
     *     path="/api/user/profile",
     *     description="Update user profile",
     *     tags={"User"},
     *     @OA\Parameter(name="id",description="User id",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="name",description="User name",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="email",description="Unique user email",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="job",description="User's job description",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="phone",description="User's phone number",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.auth.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    public function updateProfile(UpdateUserProfileRequest $request) {
        $user = Auth::user();
        $user->update($request->toArray());
        return $this->successResponse(compact('user'));
    }

    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     description="Get user profile",
     *     tags={"User"},
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.auth.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function getProfile() {
        $user = Auth::user();
        return $this->successResponse(new UserProfileResource($user));
    }
}
