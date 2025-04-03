<?php

declare(strict_types=1);

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

final class OrganizationStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:organizations,email',
            'phone' => 'nullable|string|max:20|unique:organizations,phone',
            'logo' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',

        ];
    }
}
