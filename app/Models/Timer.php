<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    protected $fillable = ['player_id', 'seconds'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}