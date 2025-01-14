<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids, SoftDeletes;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
    ];
    protected $visible = [
        'name',
        'nickname',
        'email',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->likes()->detach();
            $user->comments()->delete();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    { //likesはPhotoとUser（多対多）の中間テーブルであることを表現
        return $this->belongsToMany('App\Models\Photo', 'likes');
    }
}
