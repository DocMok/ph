<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetInvestorsRequest;
use App\Http\Requests\Api\LikedInvestorsRequest;
use App\Http\Requests\Api\GetInvestorRequest;
use App\Http\Requests\Api\InvestorLikeRequest;
use App\Http\Resources\InvestorProfileResource;
use App\Http\Resources\InvestorResource;
use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponsable;
use App\Models\Investor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InvestorController extends Controller
{
    use ApiResponsable;

    const MAX_ITEMS_PER_PAGE = 20;

    /**
     * @OA\Get(
     *     path="/api/investors",
     *     description="Get investors with/without filters",
     *     tags={"Investors"},
     *     @OA\Parameter(name="category_ids",description="Array of category ids",required=false,in="query",@OA\Schema(type="json")),
     *     @OA\Parameter(name="currency",description="Currency",required=false,in="query",@OA\Schema(type="string")),
     *     @OA\Parameter(name="min",description="Min amount to invest",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="max",description="Max amount to invest",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit",description="Projects per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/investors.list.response")),
     *     security={{"Authorization": {}}}
     * )
     */

    /**
     * @OA\Schema(schema="investors.list.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="object",
     *      @OA\Property(property="pages_total", type="integer", example=3),
     *      @OA\Property(property="investors", type="array",
     *          @OA\Items(ref="#/components/schemas/investor.response"),
     *      ),
     *   ),
     * )
     */
    public function index(GetInvestorsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $investorsQuery = Investor::filter($request, $user);

        $investorsTotal = $investorsQuery->count();
        $investors = $investorsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($investorsTotal / $limit),
            'investors' => InvestorResource::collection($investors),
        ];

        return $this->successResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/investor",
     *     description="Get investor profile",
     *     tags={"Investors"},
     *     @OA\Parameter(name="id",description="User id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/investor.profile.for.other.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function show(GetInvestorRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $investorUser = User::find($request->id);
        if ($investorUser->user_type != User::INVESTOR) {
            return $this->errorResponse('User is not investor');
        }

        $investor = Investor::find($investorUser->typeable->id);
        return $this->successResponse(new InvestorProfileResource($investor));
    }

    /**
     * @OA\Post(
     *     path="/api/investor/like-toggle",
     *     description="Like investor",
     *     tags={"Investors"},
     *     @OA\Parameter(name="id",description="User id",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="unread_messages",description="User unread messages count. Min: 0.",required=true,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/like.toggle.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function likeToggle(InvestorLikeRequest $request, NotificationService $NS)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $investorUser = User::find($request->id);
        if ($investorUser->user_type != User::INVESTOR) {
            return $this->errorResponse('User is not investor');
        }

        $investorUser->update(['unread_messages' => $request->unread_messages]);
        $investor = Investor::find($investorUser->typeable->id);
        $toggleResult = $investor->likes()->toggle($user->id);

        if (!empty($toggleResult['attached'])) {
            $title = config('app.name'). ' notification';
            $body = 'Somebody liked your profile recently';

            $NS->notificate($user, $investorUser, $investor, $title, $body, 'Liked your profile');
        }

        $response = ['likes_total' => $investor->likes()->count()];

        return $this->successResponse($response);
    }

    /**
     * @OA\Get(
     *     path="/api/investors/liked",
     *     description="Liked investors",
     *     tags={"Investors"},
     *     @OA\Parameter(name="limit",description="Investors per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/investors.list.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function liked(LikedInvestorsRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->errorResponse('User is not authorized');
        }

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $investorsQuery = $user->likedInvestors();
        $investorsTotal = $investorsQuery->count();
        $investors = $investorsQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($investorsTotal / $limit),
            'investors' => InvestorResource::collection($investors),
        ];
        return $this->successResponse($response);
    }
}
