<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\GetUserNotificationsRequest;
use App\Http\Resources\NoticeResource;
use App\Http\Traits\ApiResponsable;
use Illuminate\Support\Facades\Auth;

class UserNotificationController extends Controller
{
    use ApiResponsable;

    const MAX_ITEMS_PER_PAGE = 20;

    /**
     * @OA\Get(
     *     path="/api/user/notifications",
     *     description="User notifications",
     *     tags={"Notifications"},
     *     @OA\Parameter(name="limit",description="Notifications per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/user.notifications.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="user.notifications.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",
     *      @OA\Property(property="pages_total", type="integer", example=3),
     *      @OA\Property(property="notifications", type="array",
     *          @OA\Items(ref="#/components/schemas/notice.response"),
     *      ),
     *   ),
     * )
     */
    public function index(GetUserNotificationsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $notificationsQuery = $user->notices();

        $notificationsTotal = $notificationsQuery->count();
        $notifications = $notificationsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($notificationsTotal / $limit),
            'notifications' => NoticeResource::collection($notifications),
        ];

        return $this->successResponse($response);
    }
}
