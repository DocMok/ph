<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'project' => new ProjectResource($this),
            'owner' => new UserResource($this->projectOwner->user),
        ];
    }
}
