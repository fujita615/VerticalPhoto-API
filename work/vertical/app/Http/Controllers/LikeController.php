<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        //ユーザー認証が必要
        $this->middleware('auth');
    }
    //中間テーブルのlikeテーブルを追加するメソッド
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo) {
            abort(404);
        }
        // 一度しかいいねできないようにlikeレコードを削除・追加を１セットで行う
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);
        //photo_idがjson形式で返却される
        return ["photo_id" => $id];
    }

    //中間テーブルのlikeテーブルを削除するメソッド
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();
        if (!$photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);
        return ["photo_id" => $id];
    }
}
