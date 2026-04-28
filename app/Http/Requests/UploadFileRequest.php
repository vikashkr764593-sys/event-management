<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UploadFileRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() { return ['file' => 'required|file|max:20480']; }
}