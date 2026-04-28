<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:10|max:15',
            'otp'   => 'required|string|size:6',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'A phone number is required.',
            'otp.required'   => 'The 6-digit OTP is required.',
            'otp.size'       => 'The OTP must be exactly 6 digits long.',
        ];
    }
}