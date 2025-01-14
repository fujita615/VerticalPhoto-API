<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    //idをulidに設定
    use HasFactory;
    use HasUlids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    //フォームから一括代入できるカラムを指定
    protected $fillable = [
        'content',
        'user_id',
        'photo_id',
    ];

    //userと多対一のリレーションを張る
    public function author()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }
    //photoと多対一のリレーションを張る
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // アクセサ:コメント投稿をしているuserかどうか(boolean)を表すカラムcommented_by_author
    public function getCommentedByAuthorAttribute()
    {
        //非ログイン状態ならfalse
        if (Auth::guest()) {
            return false;
        }
        if ($this->user_id  === Auth::user()->id) {
            return true;
        } else {
            return false;
        }
    }

    //jsonレスポンスに含めるアクセサを明示
    protected $appends = ['commented_by_author'];
    //responseに渡す属性を指定
    protected $visible = ['content', 'author', 'photo', 'commented_by_author'];
}
