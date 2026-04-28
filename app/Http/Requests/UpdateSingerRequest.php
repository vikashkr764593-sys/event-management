<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateSingerRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'genre' => 'sometimes|string',
            'experience' => 'sometimes|integer',
        ];
    }
}