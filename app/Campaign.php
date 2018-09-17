<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{

    public function template(){
        return $this->belongsToMany('\App\MailEditor','campaign_template','campaign_id','template_id');
    }
    public function contact(){
        return $this->belongsToMany('\App\Contact','campaign_contact','campaign_id','contact_id')->withPivot('status');
    }
    public function schedule(){
        return $this->hasOne('\App\Schedule');
    }
}
