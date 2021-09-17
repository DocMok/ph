<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetInvestorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'limit' => 'integer|min:1',
            'page' => 'integer|min:1',
            'category_ids' => 'array',
            'category_ids.*' => 'integer',
            'currency' => 'string',
            'min' => 'integer|min:0',
            'max' => 'integer|min:0',
        ];
    }
}
