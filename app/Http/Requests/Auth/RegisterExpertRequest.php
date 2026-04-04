<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\ExpertInvitation;

class RegisterExpertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'                 => 'required|string|uuid',
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:150|unique:experts,email',
            'password'              => 'required|string|min:12|max:255|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    public function withValidator($validator): void
    {
        // Validate the invitation token inside the FormRequest
        // so the controller stays thin and errors are in the validation layer
        $validator->after(function ($validator) {
            $invitation = ExpertInvitation::where('token', $this->token)->first();

            if (! $invitation || ! $invitation->isValid()) {
                $validator->errors()->add('token', 'Invitation link is invalid or has expired.');
            }
        });
    }
}
