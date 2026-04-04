<?php

namespace App\Http\Requests\Expert;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSeverityLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label'       => 'required|string|max:100',
            'min_score'   => 'required|integer|min:0|lt:max_score',
            'max_score'   => 'required|integer|min:0|gt:min_score',
            'description' => 'nullable|string|max:500',
            'color_hex'   => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }
}
