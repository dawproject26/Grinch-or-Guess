<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roulette extends Model
{
    protected $table = 'roulette';

    protected $fillable = ['option'];
}