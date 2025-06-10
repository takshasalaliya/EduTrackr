<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Optional_subject;
use Maatwebsite\Excel\Concerns\ToCollection;

class OptionalImport implements ToCollection,ToModel
{
    private $current=0;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        
    }
    public function model(array $row){
      try{
        $this->current++;
        if($this->current>1){
          $num=0;
          foreach($row as $rows){
            if($rows==NULL){
                break;
            }
            $num++;
          }
          $j=0;
          for($i=0;$i<$num;$i++){
            $student=Student::where('enrollment_number',$row[0])->first();
            $j++;
            // dd($student);
            $subject=Subject::where('class_id',$student->class_id)->where('category','optional')->get();
            $sub=[];
            // dd($subject);
            foreach($subject as $subj){
                $sub[]=$subj->subject_id;
            }
            // dd($row[3]);
            if($row[$i]=='y'){
            $validgo=Optional_subject::where('student_id',$student->student_id)->where('subject_id',$sub[$i-2])->first();
            // dd($validgo);
            if(empty($validgo)){
            $data=new Optional_subject();
            $data->student_id=$student->student_id;
            $data->subject_id=$sub[$i-2];
            $data->save();
            $id=Student::where('student_id',$student->student_id)->first();
            $id->optional='yes';
            $id->save();
            }
            }
        
          }
           
        }
      }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
