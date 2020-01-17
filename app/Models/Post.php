<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'image', 'caption'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class,'user_id','id');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class,'post_id','id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class,'post_id','id');
    }
}
