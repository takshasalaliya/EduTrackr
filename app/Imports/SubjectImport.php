<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Subject;
use App\Models\Program;
use App\Models\Student_class;

class SubjectImport implements ToCollection,ToModel
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
            $find=explode('/',$row[3]);
            $stream=Program::where('name',$find[0])->first();
            $date=explode(" ",end($find));
            $data=Student_class::where('stream',$stream->program_id)->where('sem',$find[1])->where('devision',$find[2])->first();
    $class= new Subject();
    $class->subject_name=$row[0];
    $class->short_name=$row[1];
    $class->subject_code=$row[2];
    $class->category=$row[4];
    $class->class_id=$data->id;
    $class->group=$row[6];
    $class->lecture_category=$row[5];
    $class->save();
        }
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
