<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teaching_staff extends Model
{
    //
    

    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id','subject_id');
    }
    public function teacher(){
        return $this->belongsTo(User::class,'staff_id');
    }
    public function attendence(){
        return $this->hasMany(Attendence::class,'staff_id','id');
    }
    public function Teaching_staff(){
        return $this->hasMany(Teaching_hour::class,'staff_id');
    }
    public function timetable(){
        return $this->hasMany(Timetable::class,'subject');
    }
}
