<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'job' => 'required|string',
            'phone' => 'required|numeric|unique:users,phone',
            'password' => 'required|string',
            'user_type' => 'required|numeric|min:0|max:1',
        ];
    }
}