<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OneProjectResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'project' => new ProjectResource($this),
            'owner' => new UserResource($this->projectOwner->user),
        ];
    }
}
