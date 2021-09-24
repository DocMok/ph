<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NotificationTokenStoreRequest;
use App\Http\Traits\ApiResponsable;
use App\Models\NotificationToken;
use Illuminate\Support\Facades\Auth;

class NotificationTokenController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Post(
     *     path="/api/user/fcm-tokens",
     *     description="Add user fcm token",
     *     tags={"FCM tokens"},
     *     @OA\Parameter(name="token",description="token",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/fcm.token.store.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="fcm.token.store.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="string", example="ok"),
     *   )
     */
    public function store(NotificationTokenStoreRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        NotificationToken::create([
            'user_id' => $user->id,
            'token' => $request->token,
        ]);

        return $this->successResponse('ok');
    }

    /**
     * @OA\Get(
     *     path="/api/user/fcm-tokens",
     *     description="Get user fcm tokens",
     *     tags={"FCM tokens"},
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/fcm.token.get.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="fcm.token.get.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="array", example={"token1", "token2"},@OA\Items(type="string")),
     *   )
     */
    public function index() {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $tokens = $user->notificationTokens->pluck(['token']);
        return $this->successResponse($tokens);
    }
}
