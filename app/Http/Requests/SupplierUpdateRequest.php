<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SupplierUpdateRequest extends FormRequest
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
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'email' => 'required|email|unique:supplier,email,' . $this->id,
            'phone' => 'required|unique:supplier,phone,' . $this->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'Taxcode' => 'nullable|string|max:20',
            'curentBalance' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:255',
        ];
    }
    public function messages(): array
    {
        return [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter valid email address.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number already exists.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'address.string' => 'Address must be a string.',
            'city.string' => 'City must be a string.',
            'Taxcode.string' => 'Tax code must be a string.',
            'curentBalance.numeric' => 'Current balance must be a number.',
        ];
    }
}
