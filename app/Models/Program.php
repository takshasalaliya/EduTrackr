<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    //
    protected $primaryKey = 'program_id';

    public function student_class(){
        return $this->hasMany(Student_class::class,'stream','program_id');
    }
    use HasFactory;
}
