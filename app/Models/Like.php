<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id','post_id'];

    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class,'post_id','id');
    }
}
