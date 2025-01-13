<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
{
    /** 要ログイン  */
    public function authorize(): bool
    {
        if (Auth::check()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:30'],
            'nickname' => ['required', 'string', 'max:15', Rule::unique('users')->ignore(Auth::id())],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
        ];
    }
    public function messages()
    {
        return
            [
                'nickname.required' => 'ユーザー名は必ず入力してください',
                'nickname.unique' => '既に利用されているユーザー名です',
                'email.unique' => '既に登録されているメールアドレスです',
                'nickname.max' => 'ユーザー名の文字数上限を超えています',
            ];
    }
}
