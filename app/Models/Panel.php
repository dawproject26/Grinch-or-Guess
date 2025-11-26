<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = ['title'];

    public function phrases()
    {
        return $this->hasMany(Phrase::class);
    }
}