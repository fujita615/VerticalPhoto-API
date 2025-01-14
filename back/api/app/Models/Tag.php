<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    //フォームからの保存・更新を許可するカラム
    protected $fillable = ['photo_id', 'name'];

    //photoとのリレーション（多対多　中間テーブル有り）
    public function photos()
    {
        return $this->belongsToMany('App\Models\Photo', 'photo_tag')->withTimestamps();
    }
    //レスポンスに渡す（見せる）属性を指定
    protected $visible = ['name'];
}
