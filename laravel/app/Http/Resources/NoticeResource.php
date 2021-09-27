<?php

namespace App\Http\Resources;

use App\Models\Notice;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'from_user' => new UserResource($this->fromUser),
            'project' => $this->when($this->notice_type == Notice::PROJECT,
                $this->notificateable ? new ProjectResource($this->notificateable) : []),
        ];
    }
}
