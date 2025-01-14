<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePhotoRequest extends FormRequest
{
    //写真投稿時のバリデーション
    /**
     * Determine if the user is authorized to make this request.
     */

    public function authorize(): bool
    {
        if (Auth::check()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //入力必須,file形式はjpg jpeg png gif
            'photo' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string|max:500',
        ];
    }
    public function messages()
    {
        return [
            'photo.max' => 'ファイルサイズオーバーです（最大2MB）',
            'photo.mimes' => '未対応の画像形式です(png,jpeg,jpg,gif形式のみ対応)',
        ];
    }
}
