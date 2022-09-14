<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OneProjectResource extends JsonResource
{
    /**
     * @OA\Schema(schema="one.project.response",
     *          @OA\Property(property="project",type="object",
     *              @OA\Property(property="id",type="integer",example=1),
     *              @OA\Property(property="name",type="string",example="Best project ever"),
     *              @OA\Property(property="description",type="string",example="Some description"),
     *              @OA\Property(property="product_or_service_description", type="string",example="Some text"),
     *              @OA\Property(property="resources_available_needed", type="string",example="Some text"),
     *              @OA\Property(property="total_time_frame_and_cost", type="string",example="Some text"),
     *              @OA\Property(property="expected_revenue_and_profits", type="string",example="Some text"),
     *              @OA\Property(property="currency",type="string",example="usd"),
     *              @OA\Property(property="amount_available",type="integer",example=1000),
     *              @OA\Property(property="amount_remaining",type="integer",example=5000),
     *              @OA\Property(property="category_id",type="integer",example=7),
     *              @OA\Property(property="logo",type="string",example="https://partnerhub.info/some/path/to/logo.jpg"),
     *              @OA\Property(property="likes_total",type="integer",example=2),
     *              @OA\Property(property="is_liked",type="boolean",example=false),
     *
     *          ),
     *          @OA\Property(property="owner",type="object",allOf={@OA\Schema(ref="#/components/schemas/user.response")}),
     * )
     */

    public function toArray($request)
    {
        return [
            'project' => [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'product_or_service_description' => $this->product_or_service_description,
                'resources_available_needed' => $this->resources_available_needed,
                'total_time_frame_and_cost' => $this->total_time_frame_and_cost,
                'expected_revenue_and_profits' => $this->expected_revenue_and_profits,
                'currency' => $this->currency,
                'amount_available' => $this->amount_available,
                'amount_remaining' => $this->amount_remaining,
                'category_id' => $this->category_id,
                'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
                'likes_total' => $this->likes()->count(),
                'is_liked' => $this->likes()->where('user_id', Auth::user()->id)->count() == 1,
            ],
            'owner' => new UserResource($this->projectOwner->user),
        ];
    }
}
