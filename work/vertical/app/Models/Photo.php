<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;



    protected $fillable = [

        'category_id',
    ];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($photo) {
            $photo->comments()->delete();
            $photo->likes()->detach();
            $photo->tags()->detach();
        });
    }


    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'photo_tag')->orderBy('photo_tag.id', 'desc')->withTimestamps();
    }
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('updated_at', 'desc');
    }
    public function likes()
    { //likesはPhotoとUser（多対多）の中間テーブルであることを表現
        // （withTimeStampsでlikesテーブルのtimestampを更新するよう設定）
        return $this->belongsToMany('App\Models\User', 'likes')->withTimestamps();
    }


    //アクセサという仕組みを使ってユーザから預かった以外の新項目urlを追加する
    public function getUrlAttribute()
    {
        return Storage::url('thumbnail/' . $this->attributes['filename']);
    }

    //アクセサ:like(いいね)数を表す新項目likes_countとして追加
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }
    //アクセサ:いいねをしているuserかどうか(boolean)を表す新項目liked_by_user項目を追加する

    public function getLikedByUserAttribute()
    {
        // ログインしていない状態ならfalse
        if (Auth::guest()) {
            return false;
        }
        //現在のログインユーザーidがlikesテーブルのuser_idに含まれているか(contains)
        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

    //アクセサ:Photo投稿をしているuser（本人）かどうか(boolean)を表す新項目posted_by_user項目を追加する
    public function getPostedByUserAttribute()
    {
        // ログインしていない状態ならfalse
        if (Auth::guest()) {
            return false;
        }
        //現在のログインユーザーidがuser_idと一致するか
        return $this->user_id  === Auth::user()->id;
    }

    //アクセサ:このPhotoにコメント投稿をしているuserかどうか(boolean)を表す新項目commented_by_user項目を追加する
    public function getCommentedByUserAttribute()
    {
        // ログインしていない状態ならfalse
        if (Auth::guest()) {
            return false;
        }
        //現在のログインユーザーidがuser_idと一致するか(contains)
        return $this->comments->contains(function ($comments) {
            return $comments->user_id  === Auth::user()->id;
        });
    }

    protected $appends = ['url', 'likes_count', 'liked_by_user', 'posted_by_user', 'commented_by_user'];
    //jsonに渡す（見せる）属性を指定
    protected $visible = ['id', 'owner', 'url', 'comments', 'likes_count', 'liked_by_user', 'posted_by_user', 'commented_by_user', 'tags'];

    //1ページに表示する件数（jsonに含まれる情報）
    // protected $perPage = 1200;
}
