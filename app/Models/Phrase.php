<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phrase extends Model
{
    protected $table = 'phrases';

    protected $fillable = [
        'movie',
        'phrase',
        'panel_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }
}