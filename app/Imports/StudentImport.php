<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Student;
use App\Models\Student_class;
use App\Models\Program;
use App\Models\User;
use App\Models\Demo;
use Illuminate\Validation\ValidationException;

class StudentImport implements ToCollection,ToModel
{
    private $current=0;
    /**
    * @param Collection $collection
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws ValidationException
    */
    public function collection(Collection $collection)
    {
        //
        
    }
    public function model(array $row){
        try{
        $this->current++;
        if( $this->current>1){
            $count=Student::where('enrollment_number','=',$row[0])->count();
            if(empty($count)){
                for ($i=0; $i<=6 ; $i++) { 
                   if(empty($row[$i])){
                    throw ValidationException::withMessages([
                        'error' => 'Please upload a valid file with all required fields.'
                    ]);
                   }
                }
            $i=0;
            $duplicate=Student::where('email',$row[3])->first();
            if(!empty($duplicate->id)){
                $i=1;
            }
            if($i==0){
            $valid=explode('/',$row[6]);
            $id=Program::where('name',$valid[0])->first();
            $class_id=Student_class::where('stream',$id->program_id)->where('sem',$valid[1])->where('devision',$valid[2])->first();
            $student=new Student();
            $student->name=$row[1];
            $student->enrollment_number=$row[0];  
            $student->phone_number=$row[2];
            $student->email=$row[3];
            $student->parents_phone_number=$row[4];
            $student->parents_email=$row[5];
            $student->class_id =$class_id->id;
            $student->save();

            $usersdata=new User();
            $usersdata->name=$row[1];
            $usersdata->short_name=$row[0];
            $usersdata->email=$row[3];
            $usersdata->phone_number=$row[2];
            $usersdata->role="student";
            $usersdata->password=$row[0];
            $usersdata->plain_password=$row[0];
            $usersdata->save();
            }
            }
        }
    }catch (\Throwable $e) {
        return;
    }
    }
}
