<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //上書きの準備としてFortifyのルートをまず全削除
        Fortify::ignoreRoutes();

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                // 新規登録成功時は201コードとuser情報を返却する
                $user = $request->user();

                return response($user, 201);
            }
        });

        //ログイン処理後
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // ログイン成功時は200コードとuser情報を返却する
                $user = $request->user();

                return response($user, 200);
            }
        });
        //ユーザー情報更新後
        $this->app->instance(ProfileInformationUpdatedResponse::class, new class implements ProfileInformationUpdatedResponse {
            public function toResponse($request)
            {
                // 更新成功時は201コードとuser情報を返却する
                $user = $request->user();

                return response($user, 201);
            }
        });
        // ログアウト処理後は204コードを返却する
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return response([], 204);
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
