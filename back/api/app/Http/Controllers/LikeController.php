<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{

    //いいね！するメソッド
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();
        //写真データがなかったり写真の投稿者だった場合は処理終了
        if (!$photo || Auth::user()->id === $photo->user_id) {
            abort(404);
        }
        // 一度しかいいねできないようにlikeレコードを削除・追加を１セットで行う
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);
        //photo_idがjson形式で返却される
        return ["photo_id" => $id];
    }

    //いいね！を削除するメソッド
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();
        //写真データがなかったり写真の投稿者だった場合は処理終了
        if (!$photo || Auth::user()->id === $photo->user_id) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        return ["photo_id" => $id];
    }
}
