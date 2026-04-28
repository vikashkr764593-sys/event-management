<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateBookingRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() { return ['event_date' => 'sometimes|date', 'time_slot' => 'sometimes|string', 'event_type' => 'sometimes|string', 'notes' => 'nullable|string']; }
}