<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthSigninRequest extends GeneralRequest
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
            'username' => 'required|min:4|max:60|string',
            'password' => "required|min:8|max:$passwordMaxLength|string"
        ];
    }
}
