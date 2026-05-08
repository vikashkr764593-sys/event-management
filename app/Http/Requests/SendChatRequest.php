<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SendChatRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() { 
        return [
            'booking_id' => 'required_without:order_id|exists:bookings,id', 
            'order_id'   => 'required_without:booking_id|exists:orders,id',
            'message'    => 'required|string|max:1000'
        ]; 
    }
}