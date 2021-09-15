<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * @OA\Schema(schema="project.owner.profile.response",
     *     @OA\Property(property="id", type="integer",example=1),
     *     @OA\Property(property="user_type", type="integer",example=0),
     *     @OA\Property(property="name", type="string",example="John Dou"),
     *     @OA\Property(property="phone", type="string",example="380501234578"),
     *     @OA\Property(property="email", type="string",example="mail@test.com"),
     *     @OA\Property(property="job", type="string",example="Backend developer"),
     *     @OA\Property(property="projects", type="array",
     *          @OA\Items(
     *              @OA\Property(property="name", type="string", example="Project1"),
     *          ),
     *     ),
     * )
     */

    /**
     * @OA\Schema(schema="investor.profile.response",
     *     @OA\Property(property="id", type="integer",example=3),
     *     @OA\Property(property="user_type", type="integer",example=1),
     *     @OA\Property(property="name", type="string",example="John Dou"),
     *     @OA\Property(property="phone", type="string",example="380501234578"),
     *     @OA\Property(property="email", type="string",example="mail@test.com"),
     *     @OA\Property(property="job", type="string",example="Backend developer"),
     *     @OA\Property(property="amount", type="integer",example=20000),
     *     @OA\Property(property="currency", type="string",example="usd"),
     * )
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
            'amount' => $this->when($this->user_type == 1, $this->typeable->amount),
            'currency' => $this->when($this->user_type == 1, $this->typeable->currency),
            'projects' => $this->when($this->user_type == 0, $this->typeable->projects),
        ];
        return $result;
    }
}
