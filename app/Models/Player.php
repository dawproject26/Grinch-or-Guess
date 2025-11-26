<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name', 
        'idavatar'
    ];

    public function score()
    {
        return $this->hasOne(Score::class);
    }

    public function timer()
    {
        return $this->hasOne(Timer::class);
    }
}