<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable=['resv_id','checkin','checkout','room','room_type','revenue','status'];
    //
    public function contact(){
        return $this->belongsToMany('\App\Contact','contact_transaction','transaction_id','contact_id','id','contactid');
    }
    public function roomType(){
        return $this->hasOne('\App\RoomType','room_code','room_type');
    }

}
