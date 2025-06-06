<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Student_class extends Model
{
    //
    public function teacher()
    {
        return $this->belongsTo(User::class, 'coundelor_id');
    }
    public function student ()
    {
        return $this->hasMany(Student::class, 'class_id', 'id');
    }

    public function subject(){
        return $this->hasMany(Subject::class,'class_id','id');
    }
    public function attendence(){
        return $this->hasMany(Attendence::class,'class_id','id');
    }
    public function timetable(){
        return $this->hasMany(Timetable::class,'teacher_id');
    }
    public function program(){
        return $this->belongsTo(Program::class,'stream','program_id');
    }
    use HasFactory;
}
