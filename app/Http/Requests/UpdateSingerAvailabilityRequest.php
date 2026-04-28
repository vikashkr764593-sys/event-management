<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSingerAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'   => 'sometimes|date|after_or_equal:today',
            'status' => 'sometimes|string|in:Available,Busy',
        ];
    }
}