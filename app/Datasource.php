<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Datasource extends Model
{
    //
    public function company(){
        return $this->belongsToMany('\App\Company');
    }
    public function contact(){
        return $this->belongsToMany('\App\Contact');
    }
}
