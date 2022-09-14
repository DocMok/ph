<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = 'user';

    /**
     * @OA\Schema(schema="user.response",
     *      @OA\Property(property="user", type="object",
     *          @OA\Property(property="id", type="integer",example=1),
     *          @OA\Property(property="user_type", type="string",example="ProjectOwner"),
     *          @OA\Property(property="name", type="string",example="John Dou"),
     *          @OA\Property(property="phone", type="string",example="380501234578"),
     *          @OA\Property(property="email", type="string",example="mail@test.com"),
     *          @OA\Property(property="job", type="string",example="Backend developer"),
     *          @OA\Property(property="photo", type="string",example="http://100.10.100.10/path/to/photo.jpg"),
     *      ),
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_type' => $this->user_type,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'job' => $this->job,
            'photo' => $this->photo ? asset('storage/'.$this->photo) : null
        ];
    }
}
