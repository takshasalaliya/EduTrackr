<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Optional_subject extends Model
{
    //
    public function student(){
        return $this->belongsTo(Student::class,'student_id','student_id');
    }
    public function subject(){
        return $this->belongsTo(Subject::class,'subject_id','subject_id');
    }
}
