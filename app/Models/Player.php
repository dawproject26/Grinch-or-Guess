<?php
<<<<<<< HEAD

=======
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD
class Player extends Model
{
    //
}
=======

class Player extends Model
{
protected $fillable = ['name'];


public function score()
{
return $this->hasOne(Score::class);
}


public function timer()
{
return $this->hasOne(Timer::class);
}
}
>>>>>>> ed8225db0ed5e46e9eadb2935b1cf6ca9e49c762
