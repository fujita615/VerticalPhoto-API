<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //コメント登録機能
    public function addComment(Photo $photo, StoreCommentRequest $request)
    {
        //写真の投稿者本人によるコメントは不可
        if (!$photo || Auth::user()->id === $photo->user_id) {
            abort(404);
        }
        //既にコメントしているユーザーによるコメントは不可
        $comment = Comment::where('user_id', Auth::user()->id)->where('photo_id', $photo->id)->first();
        if ($comment) {
            abort(429);
        }
        $comment =  new Comment();
        $comment->content = $request->content;
        $comment->user_id = Auth::user()->id;
        //photoモデルのcomments()リレーション経由でデータを登録すると関係性が紐付けされる
        $photo->comments()->save($comment);

        $new_comment = Comment::where('id', $comment->id)->with(['author:id,nickname'])->first();
        return response($new_comment, 201);
    }
    //コメント編集機能
    public function editComment(Comment $comment, string $id, StoreCommentRequest $request)
    {
        $comment = Comment::where('user_id', Auth::user()->id)->where('photo_id', $id)->first();
        $comment->content = $request->content;
        $comment->save();

        $new_comment = Comment::where('id', $comment->id)->with(['author:id,nickname'])->first();
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
}
