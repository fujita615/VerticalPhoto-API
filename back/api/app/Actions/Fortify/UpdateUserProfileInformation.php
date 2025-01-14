<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'nickname.required' => 'ユーザー名は必ず登録してください',
            'nickname.max' => '登録文字上限を超えています',
            'nickname.unique' => '既に利用されているユーザー名です',
            'email.unique' => '既に登録されているメールアドレスです',
        ])->validateWithBag('updateProfileInformation');

        //mail通知機能はOFF
        // if (
        //     $input['email'] !== $user->email &&
        //     $user instanceof MustVerifyEmail
        // ) {
        //     $this->updateVerifiedUser($user, $input);
        // } else {
        $user->forceFill([
            'name' => $input['name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
        ])->save();
        // }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
