<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfirmEmail extends Model
{
    protected $table='checkin_confirm';
    protected $fillable=['template_id','active','sendafter'];

    public function template(){
        return $this->belongsTo('\App\MailEditor');
    }
}
