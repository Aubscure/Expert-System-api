<?php

namespace App\Http\Requests\Expert;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'              => 'sometimes|required|string|max:255',
            'description'        => 'sometimes|nullable|string|max:500',
            'has_essay_question' => 'sometimes|boolean',
            'essay_prompt'       => 'sometimes|nullable|string|max:500',
        ];
    }
}
