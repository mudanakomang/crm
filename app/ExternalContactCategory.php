<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalContactCategory extends Model
{
    //
    protected $fillable=['category'];
    public function email(){
        return $this->belongsToMany(\App\ExternalContact::class);
    }
    public function campaign(){
        return $this->belongsToMany(\App\Campaign::class);
    }
}
