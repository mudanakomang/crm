<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalContact extends Model
{
    //
    protected $fillable=['email','fname','lname'];
    public function category(){
        return $this->belongsToMany(\App\ExternalContactCategory::class);
    }
    public function campaign (){
        return $this->belongsToMany('\App\Campaign','campaign_external_contact','external_contact_id')->withPivot('status');
    }
}
