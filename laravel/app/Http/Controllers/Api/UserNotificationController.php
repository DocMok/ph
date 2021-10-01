<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetUserNotificationsRequest;
use App\Http\Requests\Api\UpdateNotificationsRequest;
use App\Http\Resources\NoticeResource;
use App\Http\Traits\ApiResponsable;
use App\Models\Notice;
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
     *          @OA\Items(oneOf={
     *              @OA\Schema(ref="#/components/schemas/notice.response.with.project"),
     *              @OA\Schema(ref="#/components/schemas/notice.response")},
     *          ),
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

        $notificationsQuery = $user->notices()->orderBy('created_at', 'desc');

        $notificationsTotal = $notificationsQuery->count();
        $notifications = $notificationsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($notificationsTotal / $limit),
            'notifications' => NoticeResource::collection($notifications),
        ];

        return $this->successResponse($response);
    }

    /**
     * @OA\Put(
     *     path="/api/user/notifications",
     *     description="Update is_viewed notifications status",
     *     tags={"Notifications"},
     *     @OA\Parameter(name="notification_ids",description="Array of notification ids",required=true,in="query",@OA\Schema(type="json")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/update.notifications.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    /**
     * @OA\Schema(schema="update.notifications.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",example="ok"),
     * )
     */
    public function update(UpdateNotificationsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }
        $notificationIds = json_decode($request->notification_ids);
        $updatedCount = Notice::whereIn('id', $notificationIds)->update(['is_viewed' => true]);
        return count($notificationIds) == $updatedCount
            ? $this->successResponse('ok')
            : $this->errorResponse('Some notifications have not been updated');
    }
}
