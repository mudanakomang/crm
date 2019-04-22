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
    public function segment(){
        return $this->belongsToMany('\App\Segment','campaign_segment','campaign_id','segment_id');
    }
    public  function emailresponse(){
        return $this->hasMany('\App\EmailResponse');
    }

    public function external(){
        return $this->belongsToMany('\App\ExternalContact','campaign_external_contact','campaign_id','external_contact_id')->withPivot('status');
    }
    public function externalSegment(){
        return $this->belongsToMany(\App\ExternalContactCategory::class);
    }
}
