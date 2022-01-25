<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use Illuminate\Http\JsonResponse;

class RegisterRequest extends FormRequest
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
        $rules['first_name']    = "required";
        $rules['last_name']     = "required";
        $rules['email']         = "required|unique:App\Models\User,email|email";
        $rules['password']      = "required";
        $rules['country']       = "required";

        return $rules;
    }

    protected function errorResponse(): ?JsonResponse
    {
        return response()->json([
            'result' => false,
            'reason' => $this->validator->errors()->messages(),
        ], $this->statusCode());
    }

    // public function messages(): array
    // {
    //     return[
    //         'first_name.required'       => 'El campo first_name es obligatorio',
    //         'last_name.required'    => 'El campo last_name es obligatorio',
    //     ];
    // }

}
