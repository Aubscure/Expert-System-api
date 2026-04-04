<?php

namespace App\Http\Requests\Expert;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body'        => 'required|string|max:255',
            'score_value' => 'required|integer|min:0|max:10',
        ];
    }
}
