<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $primaryKey = 'subject_id';

    public function student_class(){
        return $this->belongsTo(Student_class::class,'class_id');
    }

    public function teaching_staff(){
        return $this->hasMany(Teaching_staff::class,'subject_id','subject_id');
    }
    public function optional_subject(){
        return $this->hasMany(Optional_subject::class,'subject_id','subject_id');
    }

}
