<?php

<<<<<<< HEAD
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phrase extends Model
{
    protected $table = 'phrases';

    protected $fillable = [
        'movie',
        'phrase'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
=======

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class Phrase extends Model
{
protected $fillable = ['panel_id', 'phrase'];


public function panel()
{
return $this->belongsTo(Panel::class);
}
}
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
