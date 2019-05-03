<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['category', 'count', 'user_id'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
