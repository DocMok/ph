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
     *          @OA\Property(property="currency", type="string",example="usd"),
     *          @OA\Property(property="amount_available", type="integer",example=1000),
     *          @OA\Property(property="amount_remaining", type="integer",example=3000),
     *          @OA\Property(property="category_id", type="integer",example=7),
     *          @OA\Property(property="logo", type="string",example="http://100.10.100.10/path/to/logo.jpg"),
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
            'currency' => $this->currency,
            'amount_available' => $this->amount_available,
            'amount_remaining' => $this->amount_remaining,
            'category_id' => $this->category_id,
            'logo' => $this->logo ? asset('storage/'.$this->logo) : null,
            'likes_total' => $this->likes()->count(),
            'is_liked' => $this->likes()->where('user_id', Auth::user()->id)->count() == 1,
        ];
    }
}
