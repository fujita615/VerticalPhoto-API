<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;
    use HasUlids;


    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'content',
        'user_id',
        'photo_id',
    ];


    public function author()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // アクセサ:コメント投稿をしているuserかどうか(boolean)を表す新項目commented_by_autor項目を追加する
    public function getCommentedByAuthorAttribute()
    {
        // ログインしていない状態ならfalse
        if (Auth::guest()) {
            return false;
        }
        //現在のログインユーザーidがauthorのuser_idと一致するか(contains)
        if ($this->user_id  === Auth::user()->id) {
            return true;
        } else {
            return false;
        }
    }
    protected $appends = ['commented_by_author'];
    protected $visible = ['content', 'author', 'commented_by_author', 'photo'];
}
