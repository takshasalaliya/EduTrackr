<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    use HasFactory;

    // protected $table = 'students';
    public function class()
    {
        return $this->belongsTo(Student_class::class, 'class_id', 'id'); // Foreign key on students table
    }
    public function optional_subject(){
        return $this->hasMany(Optional_subject::class, 'student_id', 'student_id');
    }
    public function attendence(){
        return $this->hasMany(Attendence::class, 'student_id', 'student_id');
    }
    public function activity(){
        return $this->hasMany(Activity::class,'std_id','student_id');
    }
    protected $primaryKey = 'student_id';


    public $timestamps = true; // Or
}
