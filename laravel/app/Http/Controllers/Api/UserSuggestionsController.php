<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetUserSuggestionsRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Traits\ApiResponsable;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class UserSuggestionsController extends Controller
{
    use ApiResponsable;

    const MAX_ITEMS_PER_PAGE = 20;

    /**
     * @OA\Get(
     *     path="/api/user/suggestions",
     *     description="Get user suggestions",
     *     tags={"Suggestions"},
     *     @OA\Parameter(name="limit",description="Projects per page",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="page",description="Page number",required=false,in="query",@OA\Schema(type="integer")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/projects.list.response")),
     *     security={{"Authorization": {}}}
     * )
     */
    public function index(GetUserSuggestionsRequest $request)
    {
        $user = Auth::user();

        $limit = $request->limit ?? self::MAX_ITEMS_PER_PAGE;
        $page = $request->page ?? 1;
        $skip = ($page - 1) * $limit;

        $projectQuery = Project::suggestions($user);

        $projectsTotal = $projectQuery->count();
        $projects = $projectQuery->limit($limit)->skip($skip)->get();

        $response = [
            'pages_total' => (int)ceil($projectsTotal / $limit),
            'projects' => ProjectResource::collection($projects),
        ];

        return $this->successResponse($response);
    }
}
