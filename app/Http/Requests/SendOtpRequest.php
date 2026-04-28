<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:10|max:15',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'A valid phone number is required to receive an OTP.',
        ];
    }
}