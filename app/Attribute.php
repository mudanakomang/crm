<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
 public function contacts(){
        return $this->belongsToMany('\App\Contact')->withPivot('value');
    }
}
