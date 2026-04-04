<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ownership validated in service layer
    }

    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string|max:500',
            'has_essay_question'  => 'boolean',
            'essay_prompt'        => 'nullable|string|max:500|required_if:has_essay_question,true',
        ];
    }
}
