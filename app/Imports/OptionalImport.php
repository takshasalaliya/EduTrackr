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
          for($i=0;$i<$num;$i++){
            $student=Student::where('enrollment_number',$row[0])->first();
            $subject=Subject::where('class_id',$student->class_id)->where('category','optional')->get();
            $sub=[];
            foreach($subject as $subj){
                $sub[]=$subj->subject_id;
            }
            
            if($row[$i]=='y'){
            $data=new Optional_subject();
            $data->student_id=$student->student_id;
            $data->subject_id=$sub[$i-2];
            $data->save();
            }
        
          }
           
        }
      }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
