<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiValidationError;
use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'name' => 'required|string|unique:projects,name',
            'description' => 'required|string',
            'product_or_service_description' => 'string',
            'resources_available_needed' => 'string',
            'total_time_frame_and_cost' => 'string',
            'expected_revenue_and_profits' => 'string',
            'category_id' => 'required|exists:categories,id',
            'currency' => 'required|string',
            'amount_available' => 'required|integer',
            'amount_remaining' => 'required|integer',
            'logo' => 'mimes:jpg,png|max:1024',
        ];
    }
}
