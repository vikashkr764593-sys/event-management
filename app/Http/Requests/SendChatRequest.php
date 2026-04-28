<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SendChatRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() { return ['booking_id' => 'required|exists:bookings,id', 'message' => 'required|string|max:1000']; }
}