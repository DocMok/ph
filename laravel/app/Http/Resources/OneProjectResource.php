<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OneProjectResource extends JsonResource
{
    /**
     * @OA\Schema(schema="one.project.response",
     *          @OA\Property(property="project",type="object",allOf={@OA\Schema(ref="#/components/schemas/project.response")}),
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
