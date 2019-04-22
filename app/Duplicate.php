<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Duplicate extends Model
{
    public function contact(){
        return $this->hasMany('\App\Contact','email','email');
    }
}
