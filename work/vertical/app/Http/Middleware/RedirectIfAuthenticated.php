<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            //ログイン状態で非ログイン状態でのみアクセスできる機能にリクエストした場合に
            // ログイン情報を得るAPIへリダイレクトする
            if (Auth::guard($guard)->check()) {
                return redirect()->route('user');
            }
        }

        return $next($request);
    }
}
