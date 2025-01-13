<?php

use App\Http\Controllers\PhotoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|  jsonデータを要求する以外のroute定義
|--------------------------------------------------------------------------
*/


//写真をダウンロードする
Route::get('photos/{photo}/download', [PhotoController::class, 'download'])->name('photo.download');



//初期アクセス時にAPIサーバ側でlogin状態ならuserデータを渡すAPI
Route::prefix('api')->group(function () {
    Route::get('user', fn() => Auth::user())->name('user');
});

//上記以外（APIのURL以外）全てのURLでindexを返却（画面描写はindexの中でvueで変更する）
Route::get('/{any?}', fn() => view('index'))->where('any', '.+');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
