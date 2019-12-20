<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Category extends Model
{
    protected $fillable = ['category', 'count', 'user_id'];
    public $timestamps  = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
