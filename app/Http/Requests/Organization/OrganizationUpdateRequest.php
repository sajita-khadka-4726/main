<?php

declare(strict_types=1);

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class OrganizationUpdateRequest extends FormRequest
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
        $organization = $this->route('organization');

        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('organizations', 'email')->ignore($organization),
            ],
            'phone' => 'nullable|string|max:15',
            'logo' => 'nullable|string',
            'status' => 'required|boolean',
        ];
    }
}
