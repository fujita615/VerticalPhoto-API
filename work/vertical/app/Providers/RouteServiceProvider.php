<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    public const HOME = '/home';


    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        //アプリケーション起動時に読み込むルート定義が

        $this->routes(function () {
            //qpi.phpに記載されているルート定義の場合プレフィックスapiが付き、ミドルウェアwebが適用される
            Route::middleware('web')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            //web.phpに記載されているルート定義の場合ミドルウェアwebが適用される
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
