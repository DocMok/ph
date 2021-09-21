<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
     *          @OA\Property(property="is_liked", type="boolean",example=true),
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
            'is_liked' => $this->likes()->where('user_id', Auth::user()->id)->count() == 1,
        ];
    }
}
