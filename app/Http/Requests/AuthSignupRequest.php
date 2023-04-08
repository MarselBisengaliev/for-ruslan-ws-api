<?php

namespace App\Http\Requests;

class AuthSignupRequest extends GeneralRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $passwordMaxLength = pow(2, 16);

        return [
            'username' => 'required|unique:users|min:4|max:60|string',
            'password' => "required|min:8|max:$passwordMaxLength|string"
        ];
    }
}
