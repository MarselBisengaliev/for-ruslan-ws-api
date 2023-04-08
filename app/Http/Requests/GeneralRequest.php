<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GeneralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $vaildator) {
        $errors = $vaildator->errors()->messages();
        $parsedErrors = [];

        foreach ($errors as $key => $message) {
            $parsedErrors[$key] = [
                'message' => $message[0]
            ];
        }

        throw new HttpResponseException(response()->json([
            'status' => 'invalid',
            'message' => 'Request body is not valid.',
            'violations' => $parsedErrors
        ], 400));
    }
}
