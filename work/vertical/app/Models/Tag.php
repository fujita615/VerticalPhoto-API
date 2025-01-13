<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['photo_id', 'name'];

    public function photos()
    {
        return $this->belongsToMany('App\Models\Photo', 'photo_tag')->withTimestamps();
    }
    protected $visible = ['name'];
}
