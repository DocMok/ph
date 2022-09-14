<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{

    /**
     * @OA\Schema(schema="project.response",
     *          @OA\Property(property="id", type="integer",example=1),
     *          @OA\Property(property="name", type="string",example="Best project ever"),
     *          @OA\Property(property="description", type="string",example="Some description"),
     *          @OA\Property(property="product_or_service_description", type="string",example="Some text"),
     *          @OA\Property(property="resources_available_needed", type="string",example="Some text"),
     *          @OA\Property(property="total_time_frame_and_cost", type="string",example="Some text"),
     *          @OA\Property(property="expected_revenue_and_profits", type="string",example="Some text"),
     *          @OA\Property(property="currency", type="string",example="usd"),
     *          @OA\Property(property="amount_available", type="integer",example=1000),
     *          @OA\Property(property="amount_remaining", type="integer",example=3000),
     *          @OA\Property(property="category_id", type="integer",example=7),
     *          @OA\Property(property="user_id", type="integer",example=2),
     *          @OA\Property(property="user_photo", type="string",example="http://100.10.100.10/path/to/logo.jpg"),
     *          @OA\Property(property="likes_total", type="integer",example=5),
     *          @OA\Property(property="is_liked", type="boolean",example=true),
     * )
     */
    public function toArray($request)
    {
        return [
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
            'user_id' => $this->projectOwner->user->id,
            'user_photo' => $this->projectOwner->user->photo ? asset('storage/'.$this->projectOwner->user->photo) : null,
            'likes_total' => $this->likes()->count(),
            'is_liked' => $this->likes()->where('user_id', Auth::user()->id)->count() == 1,
        ];
    }
}
