<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * @OA\Schema(schema="project.owner.profile.response",
     *     @OA\Property(property="id", type="integer",example=1),
     *     @OA\Property(property="user_type", type="string",example="ProjectOwner"),
     *     @OA\Property(property="name", type="string",example="John Dou"),
     *     @OA\Property(property="phone", type="string",example="380501234578"),
     *     @OA\Property(property="email", type="string",example="mail@test.com"),
     *     @OA\Property(property="job", type="string",example="Backend developer"),
     *     @OA\Property(property="photo", type="string",example="http://100.10.100.10/path/to/photo.jpg"),
     *      @OA\Property(property="projects", type="array",
     *          @OA\Items(ref="#/components/schemas/project.response"),
     *      ),
     * )
     */

    /**
     * @OA\Schema(schema="investor.profile.response",
     *     @OA\Property(property="id", type="integer",example=3),
     *     @OA\Property(property="user_type", type="string",example="Investor"),
     *     @OA\Property(property="name", type="string",example="John Dou"),
     *     @OA\Property(property="phone", type="string",example="380501234578"),
     *     @OA\Property(property="email", type="string",example="mail@test.com"),
     *     @OA\Property(property="job", type="string",example="Backend developer"),
     *     @OA\Property(property="photo", type="string",example="http://100.10.100.10/path/to/photo.jpg"),
     *     @OA\Property(property="amount", type="integer",example=20000),
     *     @OA\Property(property="currency", type="string",example="usd"),
     *     @OA\Property(property="category_ids", type="array",@OA\Items(type="integer")),
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
            'photo' => $this->photo ? asset('storage/'.$this->photo) : null,
            'amount' => $this->when($this->user_type == User::INVESTOR, $this->typeable->amount),
            'currency' => $this->when($this->user_type == User::INVESTOR, $this->typeable->currency),
            'category_ids' => $this->when(
                $this->user_type == User::INVESTOR,
                $this->typeable->categories ? $this->typeable->categories->keyBy('id')->keys()->toArray(): []),
            'projects' => $this->when(
                $this->user_type == User::PROJECT_OWNER,
                $this->typeable->projects ? ProjectResource::collection($this->typeable->projects) : []
            ),
        ];

        return $result;
    }
}
