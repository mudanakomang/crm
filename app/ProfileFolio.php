<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileFolio extends Model
{
    //
    protected $table='profilesfolio';
    protected $fillable=['profileid','folio_master','folio','source','foliostatus'];


}
