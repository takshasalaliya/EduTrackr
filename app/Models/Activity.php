<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //
    public function user()
    {
        return $this->belongsTo(User::class,'teach_id'); // Foreign key on students table
    }
    public function student()
    {
        return $this->belongsTo(Student::class, 'std_id','student_id'); // Foreign key on students table
    }
}
