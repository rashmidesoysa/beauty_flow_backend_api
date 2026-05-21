<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'serial_number' => 'nullable|string|max:100' . $this->route('id'),
            'batch_number' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100' . $this->route('id'),
            'supplier_id' => 'nullable|exists:supplier,id',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'cost_price' => 'nullable|numeric|min:0',
            'list_price' => 'nullable|numeric|min:0',
            'avg_price' => 'nullable|numeric|min:0',
            'unit_pack' => 'nullable|numeric|min:0',
            'unit_of_measure' => 'nullable|string|max:50',
            'group_code' => 'nullable|string|max:50',
            'type_code' => 'nullable|string|max:50',
            'price_level' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
