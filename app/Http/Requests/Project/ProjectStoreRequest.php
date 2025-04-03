<?php

declare(strict_types=1);

namespace App\Http\Requests\Project;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

final class ProjectStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug',
            'organization_id' => 'required|integer|exists:organizations,id',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
            'deadline' => 'required|date',
            'created_by' => 'required|integer|exists:users,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'created_by' => Auth::id(),
        ]);
    }
}
