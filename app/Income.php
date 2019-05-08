<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = ['amount', 'comment', 'date', 'user_id'];
    public $timestamps  = false;
    public $table       = "income";

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
