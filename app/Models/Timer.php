<?php

<<<<<<< HEAD
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    //
}
=======

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
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
