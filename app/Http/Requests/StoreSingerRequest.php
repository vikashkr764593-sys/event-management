<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreSingerRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'user_id' => 'required|exists:users,id',
            'genre' => 'required|string',
            'experience' => 'required|integer',
        ];
    }
}