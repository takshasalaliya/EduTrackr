<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendence extends Model
{
    //
    public function teaching_staff(){
        return $this->belongsTo(Teaching_staff::class,'staff_id','id');    
    }
    public function student(){
        return $this->belongsTo(Student::class,'student_id','student_id');
    }
}
