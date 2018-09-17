<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Birthday extends Model
{
    //
    protected $fillable = ['sendafter','active'];
    public function template(){
        return $this->belongsTo('\App\MailEditor');
    }
}
