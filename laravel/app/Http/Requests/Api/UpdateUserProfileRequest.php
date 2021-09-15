<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'name' => 'string',
            'email' => 'email|unique:users,email,'.Auth::user()->id,
            'phone' => 'numeric|unique:users,phone,'.Auth::user()->id,
            'job' => 'string',
            'currency' => 'string',
            'amount' => 'integer',
            'category_ids' => 'array',
            'category_ids.*' => 'integer',
        ];
    }
}
