<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'id' => 'required|integer|exists:projects,id',
            'name' => 'string|unique:projects,name,'.$this->id,
            'description' => 'string',
            'category_id' => 'exists:categories,id',
            'currency' => 'string',
            'amount_available' => 'integer',
            'amount_remaining' => 'integer',
            'logo' => 'mimes:jpg,png|max:1024',
        ];
    }
}
