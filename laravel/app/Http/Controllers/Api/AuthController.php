<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\SignupRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponsable;
use App\Models\Investor;
use App\Models\ProjectOwner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Post(
     *     path="/api/user/signup",
     *     description="Signup",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                   @OA\Property(description="Item image",property="photo",type="string", format="binary"),
     *              )
     *         )
     *     ),
     *     @OA\Parameter(name="name",description="User name",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="email",description="Unique user email",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="job",description="User's job description",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="phone",description="User's phone number",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="password",description="User's password",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="user_type",description="Values: ProjectOwner, Investor",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="category_ids",description="Array of category ids. Required when user_type=Investor",required=false,in="query",
     *          @OA\Schema(type="array", @OA\Items(type="integer"))),
     *     @OA\Parameter(name="amount",description="Amount. Required when user_type=Investor",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="currency",description="Currency. Required when user_type=Investor",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.auth.response"))
     * )
     */

    /**
     * @OA\Schema(schema="user.auth.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",
     *      @OA\Property(property="user", type="object", ref="#/components/schemas/user.response"),
     *      @OA\Property(property="token", type="string", example="Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMTg3ODY3MWUyNmRkYTc3OGM1MWIxOGM2ODU0ODE3YTE4NjVlMDNkMjllNGZhZTk4NjY4OThjN2JjZjNlMGE0MWI1MjkyYTM4NmY2YTE0YzYiLCJpYXQiOjE2MzE2MDk2NTUuMTcyNzA4LCJuYmYiOjE2MzE2MDk2NTUuMTcyNzE2LCJleHAiOjE2NjMxNDU2NTUuMTU3ODE2LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.CCMMYPsit1Pet11yhPrccTGh_Aw4r6KLmCXpI_ojSkYlGqPJa_bQFkqA0q2kq1hlg8suPziF3PESWZvfFF86o-SEIhS_Ra3YQ2woL6R1ooeTUpvLouWbU1rvBuR0uZDMAT1vWklUZoL3fk4mSYdhTqI_BdTzILF0aLmuAtR08a4p3uxeqnylGQOtRjx_4D5U8wu-MvHOXzG2uqAoV3tnjMzrmq6vyQAZcYVgaKjdU5kTT_wkgcsKgM5AJ9k18Xw90Wt-7kbg3nud2DnjcUdQrlV-AxqEswTDoTMEtdVcOf99PTiAntztEaucJJAofkR2QBLWb6e2yFqor4ZUJp2YRN4aIBlwCx8FtD43JOZKq-tD1HqJxM5HS-jOtA1y_6QEP_X9WVdDgo1T0mcXCOI_JrJ50fyknsjc8Ghvt7EsLkoYCsdkJ2a1je1aSgtITeWeUyk0MEZ8qkJUyaqI2ZZP2bANFk5_0xvgRm7GYr81c1aZubEZc9CfzHCLs-SRLwoX2TyglJkUeWmd5OsjQHGxWxcgWsx3f_rbARr1DnTruJnaegPoeorIdI3eLMIi3su9dfzye7Mgx87xFt7AwUqhxzdLQIkZjNBTW-7hpibuw4miUGpwEvJsGotcJ2zHVutezM5R_fwZjShMY1GI"),
     *   ),
     * )
     */

    public function signup(SignupRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'job' => $request->job,
            'password' => Hash::make($request->password),
        ]);

        if ($request->file()) {
            $photoPath = $request->file('photo')->store("user/photos/{$user->id}", ['disk' => 'public']);
            $user->photo = $photoPath;
            $user->save();
        }

        if (!$user) {
            return $this->errorResponse('User was not created');
        }

        if ($request->user_type == User::PROJECT_OWNER) {
            $userType = ProjectOwner::create([]);
        }
        if ($request->user_type == User::INVESTOR) {
            $userType = Investor::create([
                'amount'=> $request->amount,
                'currency'=>$request->currency
            ]);
            $userType->categories()->sync($request->category_ids);
        }

        $userType->user()->save($user);

        $token = 'Bearer ' . $user->createToken('authToken')->accessToken;
        $response = ['user' => (new UserResource($user)), 'token' => $token];
        return $this->successResponse($response);

    }

    /**
     * @OA\Get(
     *     path="/api/user/login",
     *     description="Login",
     *     tags={"Auth"},
     *     @OA\Parameter(name="email",description="Unique user email",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="phone",description="User's phone number",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="password",description="User's password",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.auth.response"))
     * )
     */
    public function login(LoginRequest $request)
    {
        $loginData = ['password' => $request->password];
        $request->phone ? $loginData['phone'] = $request->phone : $loginData['email'] = $request->email;

        if (!Auth::attempt($loginData)) {
            return $this->errorResponse('Invalid credentials');
        }

        $user = Auth::user();

        if ($user) {
            $token = 'Bearer ' . $user->createToken('authToken')->accessToken;
            $response = ['user' => (new UserResource($user)), 'token' => $token];
            return $this->successResponse($response);
        }

        return $this->errorResponse('User not found');
    }

    /**
     * @OA\Get(
     *     path="/api/user/logout",
     *     description="Logout",
     *     tags={"Auth"},
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.logout.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="user.logout.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="string", example="You're logged out successfully"),
     *   )
     */

    public function logout()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }
        $user->token()->revoke();
        return $this->successResponse('You\'re logged out successfully');
    }
}

