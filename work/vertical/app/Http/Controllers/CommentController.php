<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        //ユーザー認証が必要
        $this->middleware('auth');
    }

    //コメント登録機能
    public function addComment(Photo $photo, StoreCommentRequest $request)
    {
        //
        $comment =  new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::user()->id;
        //photoモデルのcomments()リレーション経由でデータを登録すると関係性が紐付けされる
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with(['author'])->first();
        return response($new_comment, 201);
    }
    //コメント削除機能
    public function deleteComment(Comment $comment, string $id)
    {
        //
        $comment = Comment::where('user_id', Auth::user()->id)->where('photo_id', $id)->first();
        $comment->delete();

        return response(200);
    }

    //コメント編集機能
    public function editComment(Comment $comment, string $id, StoreCommentRequest $request)
    {
        $comment = Comment::where('user_id', Auth::user()->id)->where('photo_id', $id)->first();
        $comment->content = $request->content;
        $comment->save();

        $new_comment = Comment::where('id', $comment->id)->with(['author'])->first();
        return response($new_comment, 201);
    }
}
