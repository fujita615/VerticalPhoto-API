<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CasheLockMiddleware
{ //キャッシュをロックして処理を集中させないようにするミドルウェア（多重送信対策）
    public function handle(Request $request, Closure $next): Response
    {
        // GETリクエストは除外
        if ($request->method() === 'GET') {
            return $next($request);
        }
        //  ユニークなkeyを作る('route_lock_+ルート名＋セッションID')
        $key = 'route_lock_' . $request->route()->getName() . '_' . $request->session()->getId();

        //  5秒キャッシュをロックする
        $lock = Cache::lock($key, 5);
        if ($lock->get()) {
            try {
                return $next($request);
            } finally {
                $lock->release();
            }
            // ロック中に多重送信（同名KEYのリクエスト）があると、429エラーが返却される
        } else {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'Too Many Requests');
        }
    }
}
