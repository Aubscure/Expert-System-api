<?php

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses'                => 'required|array|min:1',
            'responses.*.question_id'  => 'required|integer|exists:questions,id',
            // Either a choice or essay, never both on the same response
            'responses.*.choice_id'    => 'nullable|integer|exists:choices,id',
            'responses.*.essay_text'   => 'nullable|string|max:2000',
        ];
    }
}
