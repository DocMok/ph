<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use Illuminate\Foundation\Http\FormRequest;

class LikedProjectsRequest extends FormRequest
{
    use ApiValidationError;
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
        ];
    }
}
