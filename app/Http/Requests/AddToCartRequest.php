<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'instrument_id' => 'required|exists:instruments,id',
            'quantity'      => 'integer|min:1'
        ];
    }
}
