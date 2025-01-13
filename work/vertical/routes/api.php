<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UpdateController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TagController;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//tokenリフレッシュAPI
Route::get('reflesh-token', function (Request $request) {
    $request->session()->regenerateToken();
    return response()->json();
});

//お問い合わせフォーム送信API
Route::post('contact', [ContactController::class, 'sendMail'])->name('contact');

//写真削除API
Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photo.destroy');


//いいね投稿API
Route::put('photos/{photo}/like', [LikeController::class, 'like'])->name('like.like');


//いいね消去API
Route::delete('photos/{photo}/like', [LikeController::class, 'unlike'])->name('like.unlike');

//タグ削除API
Route::delete('photos/{photo}/tags/{tagName}', [TagController::class, 'deletePhotoTag'])->name('tag.destroy');

//タグ追加API
Route::patch('photos/{photo}/tags', [TagController::class, 'addPhotoTag'])->name('tag.edit');


//コメント削除API
Route::delete('photos/{photo}/comments', [CommentController::class, 'deleteComment'])->name('comment.destroy');

//コメント編集API
Route::put('photos/{photo}/comments', [CommentController::class, 'editComment'])->name('comment.edit');

//コメント投稿API
Route::post('photos/{photo}/comments', [CommentController::class, 'addComment'])->name('comment');

//写真詳細表示API
Route::get('photos/{id}', [PhotoController::class, 'show'])->name('photo.show');


//写真一覧表示API
Route::get('photos', [PhotoController::class, 'index'])->name('photo.index');

//写真投稿API
Route::post('photos', [PhotoController::class, 'create'])->name('photo.create');

// 会員情報削除API(registerにpost(データ保存))
Route::delete('register', [UpdateController::class, 'deleteUser'])->name('user.destroy');


// 会員情報変更API(registerにpost(データ保存))
Route::put('register', [UpdateController::class, 'editPassWord'])->name('password.edit');

// 会員情報変更API(registerにpost(データ保存))
Route::patch('register', [UpdateController::class, 'update'])->name('user.update');


// ログアウトAPI(logoutにpost(データ保存)))
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ログインAPI(loginにpost(データ保存)))
Route::post('login', [LoginController::class, 'login'])->name('login');


// 会員登録API(registerにpost(データ保存))
Route::post('register', [RegisterController::class, 'register'])->name('register');




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
