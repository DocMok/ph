<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'id' => 'required|numeric|exists:users,id',
            'name' => 'string',
            'email' => 'email|unique:users,email,'.$this->id,
            'phone' => 'numeric|unique:users,phone,'.$this->id,
            'job' => 'string',
        ];
    }
}
