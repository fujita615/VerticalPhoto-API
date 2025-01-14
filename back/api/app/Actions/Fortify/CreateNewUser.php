<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:15', 'unique:users'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ], [
            'nickname.required' => 'ユーザー名は必ず登録してください',
            'nickname.max' => '登録文字上限を超えています',
            'nickname.unique' => '既に利用されているユーザー名です',
            'email.unique' => '既に登録されているメールアドレスです',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
