<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Student_class;
use App\Models\Demo;
use App\Models\Student;
use App\Models\Program;
use App\Models\Optional_subject;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teaching_hour;
use App\Models\Timetable;
use App\Models\Teaching_staff;
use App\Models\Gcode;


class MasterImport implements ToCollection,ToModel
{
    private $teacher=[];
    private $current=0;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        // dd($collection);
    }
    public function model(array $row){
        try{
       $this->current++;
       if($this->current==1){
        if($this->current==1){
            $this->teacher=$row;
        }
       }
       elseif($this->current>4){
        if($row[0]!=NULL){
        $class_array=explode('/',$row[0]);
        $pid=Program::where('name',$class_array[0])->first();
        $cid=User::where('short_name',end($class_array))->first();
        if(!empty($cid->id)){
        $valid=Student_class::where('stream',$pid->program_id)->where('year',$class_array[3])->where('sem',$class_array[1])->where('devision',$class_array[2])->where('coundelor_id',$cid->id)->first();
        if(empty($valid->id)){
        $data=new Student_class();
        $data->stream=$pid->program_id;
        $data->year=$class_array[3];
        $data->sem=$class_array[1];
        $data->devision=$class_array[2];
        $data->coundelor_id=$cid->id;
        $data->save();
        }
        $class_id=Student_class::where('stream',$pid->program_id)->where('year',$class_array[3])->where('sem',$class_array[1])->where('devision',$class_array[2])->where('coundelor_id',$cid->id)->first();
        $valid=Subject::where('subject_name',$row[1])->where('short_name',$row[3])->where('class_id',$class_id->id)->first();
        if(empty($valid->subject_id)){
        $data=new Subject();
        $data->subject_name=$row[1];
        $data->short_name=$row[3];
        $data->class_id=$class_id->id;
        $data->lecture_category=$row[5];
        if($row[4]=='C'){
        $data->category='required';
        $data->save();
        }else{
            $data->category='optional';
            $data->group=$row[2];
            $data->save();
            }
        }
        $subject_id=Subject::where('subject_name',$row[1])->where('short_name',$row[3])->where('class_id',$class_id->id)->first();
        for($i=7;$i<count($row);$i++){
            if($row[$i]!=NULL){
                $teacher_id=User::where('short_name',$this->teacher[$i])->first();
                if(!empty($teacher_id)){
                $valid=Teaching_staff::where('staff_id',$teacher_id->id)->where('subject_id',$subject_id->subject_id)->first();
                if(empty($valid->id)){
                $data=new Teaching_staff();
                $data->subject_id=$subject_id->subject_id;
                $data->staff_id=$teacher_id->id;
                $data->save();
                }
                $teaching_staff_id=Teaching_staff::where('subject_id',$subject_id->subject_id)->where('staff_id',$teacher_id->id)->first();
                $valid=Teaching_hour::where('staff_id',$teaching_staff_id->id)->first();
                if(empty($valid->id)){
                $data=new Teaching_hour();
                $data->staff_id=$teaching_staff_id->id;
                $data->hours=$row[$i];
                $data->save();
                }else{
                    $valid->hours=$row[$i];
                    $valid->save();
                }
            }
            }
        }
    }
    }
       }
       
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
