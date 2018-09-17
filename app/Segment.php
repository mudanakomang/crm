<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $fillable=['country_id','guest_status','spending_from','spending_to','stay_from','stay_to','total_stay_from','total_stay_to','total_night_from','total_night_to','gender'];
    protected $table='segments';
}
