<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    //
    public function teaching_staff(){
        return $this->belongsTo(Teaching_staff::class,'subject');
    }
    public function teacher(){
        return $this->belongsTo(User::class,'teacher_id');
    }
    public function student_class(){
        return $this->belongsTo(Student_class::class,'class_id');
    }
}
