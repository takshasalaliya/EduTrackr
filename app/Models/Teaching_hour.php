<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teaching_hour extends Model
{
    //
    public function teaching_staff(){
        return $this->belongsTo(Teaching_staff::class,'staff_id');
    }
}
