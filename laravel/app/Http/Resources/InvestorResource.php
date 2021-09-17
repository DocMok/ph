<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestorResource extends JsonResource
{
    /**
     * @OA\Schema(schema="investor.response",
     *          @OA\Property(property="id", type="integer",example=1),
     *          @OA\Property(property="name", type="string",example="Best investor ever"),
     *          @OA\Property(property="category_ids", type="array",@OA\Items(type="integer")),
     *          @OA\Property(property="currency", type="string",example="usd"),
     *          @OA\Property(property="amount", type="integer",example=1000),
     *          @OA\Property(property="photo", type="string",example="http://100.10.100.10/path/to/photo.jpg"),
     * )
     */
    public function toArray($request)
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'category_ids' => $this->categories->keyBy('id')->keys()->toArray(),
            'currency' => $this->currency,
            'amount' => $this->amount,
            'photo' => $this->user->photo ?? null,
        ];
    }
}
