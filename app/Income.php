<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = ['amount', 'comment', 'date', 'user_id'];
    protected $casts    = ['amount' => 'float'];
    public $timestamps  = false;
    public $table       = "income";

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
