<?php

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses'   => 'required|array|min:1',

            // nullable: skip exists check when question_id is null (essay row)
            // The frontend should send null, not 0 — but we coerce it in prepareForValidation
            'responses.*.question_id' => ['nullable', 'integer', Rule::exists('questions', 'id')],

            'responses.*.choice_id'  => 'nullable|integer|exists:choices,id',
            'responses.*.essay_text' => 'nullable|string|max:2000',
        ];
    }

    // Normalize the frontend's sentinel 0 into null before validation runs
    protected function prepareForValidation(): void
    {
        $this->merge([
            'responses' => collect($this->responses ?? [])->map(function (array $response) {
                // Treat 0 as "no question" — essay-only rows have no valid question_id
                if (isset($response['question_id']) && $response['question_id'] === 0) {
                    $response['question_id'] = null;
                }
                return $response;
            })->all(),
        ]);
    }
}
