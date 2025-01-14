<?php

use App\Http\Middleware\CasheLockMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            //fortifyのルート
            Route::middleware('web')
                ->name('fortyfy')
                ->prefix('api')
                ->group(base_path('routes/fortify.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //開発環境用　csrf-tokenの無効化ミドルウェア　※本番は削除
        $middleware->validateCsrfTokens(except: [
            '*',
        ]);
        //キャッシュロックをかけるミドルウェア
        $middleware->alias([
            'cacheLock'  => CasheLockMiddleware::class
        ]);
        //config/sanctum.phpで許可したフロントエンドからのアクセスはステートフル認証(セッションcookieを使って認証)するようミドルウェアを設定
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
