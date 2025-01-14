<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserDeleteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




//写真詳細表示API
Route::get('photos/{id}', [PhotoController::class, 'show'])->name('photo.show');

//写真一覧表示API
Route::get('photos', [PhotoController::class, 'index'])->name('photo.index');

//お問い合わせフォーム送信API
Route::post('contact', [ContactController::class, 'sendMail'])->name('contact')->middleware('cacheLock');

//auth:sanctum認証(webガード)を必要とするAPI
Route::group(
    ['middleware' => ['auth:sanctum', 'cacheLock']],
    function () {
        // 会員情報削除API(registerにpost(データ保存))
        Route::delete('register', [UserDeleteController::class, 'deleteUser'])->name('user.destroy');

        //タグ削除API
        Route::delete('photos/{photo}/tags/{tagName}', [TagController::class, 'deletePhotoTag'])->name('tag.destroy');

        //タグ追加API
        Route::patch('photos/{photo}/tags', [TagController::class, 'addPhotoTag'])->name('¥tag.edit')->middleware('throttle:3, 1');

        //コメント削除API
        Route::delete('photos/{photo}/comments', [CommentController::class, 'deleteComment'])->name('comment.destroy');

        //コメント編集API
        Route::put('photos/{photo}/comments', [CommentController::class, 'editComment'])->name('comment.edit');

        //コメント投稿API
        Route::post('photos/{photo}/comments', [CommentController::class, 'addComment'])->name('comment')->middleware('throttle:3, 1');

        //写真をダウンロードするAPI
        Route::get('photos/{photo}/download', [PhotoController::class, 'download'])->name('photo.download');

        //いいね投稿API
        Route::put('photos/{photo}/like', [LikeController::class, 'like'])->name('like.like');

        //いいね消去API
        Route::delete('photos/{photo}/like', [LikeController::class, 'unlike'])->name('like.unlike');

        //写真削除API
        Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photo.destroy');

        //写真投稿API
        Route::post('photos', [PhotoController::class, 'create'])->name('photo.create')->middleware('throttle:3, 1');

        //APIサーバ側でlogin状態ならuserデータを渡すAPI
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    }
);
