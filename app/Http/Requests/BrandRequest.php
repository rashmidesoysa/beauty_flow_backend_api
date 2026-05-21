<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'sub_category_id' => [
                'required',
                'integer',
                Rule::exists('sub_categories', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required',
            'name.max' => 'Brand name cannot exceed 255 characters',
            'image.image' => 'Please upload a valid image',
            'image.mimes' => 'Please upload an image of type: jpg, jpeg, png, webp',
            'image.max' => 'Image size cannot exceed 5MB',
            'category_id.required' => 'Category ID is required',
            'category_id.integer' => 'Category ID must be an integer',
            'category_id.exists' => 'The selected category does not exist or has been deleted',
            'sub_category_id.required' => 'Sub-category ID is required',
            'sub_category_id.integer' => 'Sub-category ID must be an integer',
            'sub_category_id.exists' => 'The selected sub-category does not exist or has been deleted',
        ];
    }
}
