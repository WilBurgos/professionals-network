<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Http\JsonResponse;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        $rules = array();
        if( (Auth()->user()->email != $this->header('email')) || (Crypt::decrypt(Auth()->user()->password) != $this->header('password')) ){
            $rules['auth'] = 'required';
            return $rules;
        }

        if ($this->has('first_name')) {
            $rules['first_name'] = 'required';
        }
        if ($this->has('last_name')) {
            $rules['last_name'] = 'required';
        }
        if ($this->has('email')) {
            $rules['email'] = 'required|email';
        }
        if ($this->has('password')) {
            $rules['password'] = 'required';
        }
        if ($this->has('country')) {
            $rules['country'] = 'required';
        }

        return $rules;
    }

    protected function errorResponse(): ?JsonResponse
    {
        return response()->json([
            'result' => false,
            'reason' => $this->validator->errors()->messages(),
        ], $this->statusCode());
    }

    protected function messages(): array
    {
        return[
            'auth.required'       => 'Wrong email or password',
        ];
    }
}
