<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
     public function dataSource(){
        return $this->hasMany('\App\DataSource');
    }
    public function contact(){
        return $this->hasManyThrough('\App\Contact','\App\DataSource');
    }
}
