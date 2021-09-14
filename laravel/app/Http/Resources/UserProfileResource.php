<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'user_type' => $this->user_type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'job' => $this->job,
        ];
        if ($this->user_type == 0) {
            $result['projects'] = $this->typeable->projects;
        }
        if ($this->user_type == 1) {
            $result['amount'] = $this->typeable->amount;
        }
        return $result;
    }
}
