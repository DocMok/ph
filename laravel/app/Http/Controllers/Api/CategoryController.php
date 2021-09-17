<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetCategoriesRequest;
use App\Http\Resources\CategoryArabResource;
use App\Http\Resources\CategoryEnResource;
use App\Http\Traits\ApiResponsable;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponsable;

    /**
     * @OA\Get(
     *     path="/api/projects/categories",
     *     description="Get all categories",
     *     tags={"Categories"},
     *     @OA\Parameter(name="lang",description="Language: en, arab",required=true,in="query",@OA\Schema(type="string")),
     *     @OA\Response(response=400,description="error",@OA\JsonContent(ref="#/components/schemas/errorResponse")),
     *     @OA\Response(response=200,description="ok",@OA\JsonContent(ref="#/components/schemas/get.categories.response"))
     * )
     */

    /**
     * @OA\Schema(schema="get.categories.response",
     *   @OA\Property(property="success",type="boolean",example=true),
     *   @OA\Property(property="errors_message",type="string",example=null),
     *   @OA\Property(property="data",type="array", @OA\Items(
     *      @OA\Property(property="id", type="integer",example=3),
     *      @OA\Property(property="name", type="string",example="Medical"),
     *   )),
     * )
     */
    public function getCategories(GetCategoriesRequest $request)
    {
        $categories = Category::all();
        if ($request->lang == 'arab') {
            return $this->successResponse(CategoryArabResource::collection($categories));
        }

        return $this->successResponse(CategoryEnResource::collection($categories));
    }
}
