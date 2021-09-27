<?php

namespace App\Http\Resources;

use App\Models\Notice;
use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
{

    /**
     * @OA\Schema(schema="notice.response",
     *     @OA\Property(property="id", type="integer",example=3),
     *     @OA\Property(property="text", type="string",example="Some message text"),
     *     @OA\Property(property="from_user", type="object", ref="#/components/schemas/user.response"),
     * )
     */

    /**
     * @OA\Schema(schema="notice.response.with.project",
     *     @OA\Property(property="id", type="integer",example=3),
     *     @OA\Property(property="text", type="string",example="Some message text"),
     *     @OA\Property(property="from_user", type="object", ref="#/components/schemas/user.response"),
     *     @OA\Property(property="project", type="object",ref="#/components/schemas/project.response"),
     * )
     */
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
