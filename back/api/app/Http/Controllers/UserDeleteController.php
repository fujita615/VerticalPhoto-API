<?php

namespace App\Http\Controllers;

use App\Jobs\DeletePhotoJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDeleteController extends Controller
{

    public function deleteUser()
    {
        //登録削除するユーザーの投稿した全Photoを呼び出す
        $user = User::with('photos')->find(Auth::user()->id);
        $photos = $user->photos;
        //写真を一枚ずつ写真削除非同期処理に登録する
        if ($photos) {
            foreach ($photos as $photo) {
                DeletePhotoJob::dispatch($photo)->onQueue('delete');
            }
        }
        //ユーザー登録削除
        $user->delete();
        return response(200);
    }
}
