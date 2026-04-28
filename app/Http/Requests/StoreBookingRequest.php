<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'singer_id' => 'required|exists:singers,id',
            'event_date' => 'required|date',
            'time_slot' => 'required',
            'event_type' => 'required'
        ];
    }
}
