<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use  App\Models\Attendence;
use Excel;
use App\Models\Student_class;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Activity;


class StudentClass implements FromCollection,WithHeadings,WithMapping
{

    protected $to;
    protected $from;

    public function __construct($to,$from)
    {
        //
        $this->to=$to;
        $this->from=$from;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $data=[];
            $class=Student_class::where('coundelor_id',Auth::user()->id)->get();
            foreach($class as $class){
                $class->student=Student::where('class_id',$class->id)->get();
                foreach($class->student as $student){
                    if($this->to!=0){
                        $attendent=Attendence::where('student_id',$student->student_id)->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])->get();
                    }else{
                        $attendent=Attendence::where('student_id',$student->student_id)->get();
                    }
                    $attendentas=Attendence::where('student_id',$student->student_id)->first();
                    if(empty($attendentas)){
                        continue;
                    }
                    $total=0;
                    $classCount=0;
                    $date=[];
                    foreach($attendent as $atten){
                        $classCount++;
                        $date[]=$atten->created_at;
                        if($atten->attendance=='present'){
                            $total++;
                        }
                    }       
                    sort($date);
                    $from=explode(' ',$date[0]);
                    $to=explode(' ',end($date));
                    $activity=Activity::where('std_id',$student->student_id)->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])->get();
                    $activitys=0;
                    foreach($activity as $act){
                        $activitys+=$act->session;
                    }
                    // return $activitys;
                    $percentage = ($classCount == 0) ? '0' : number_format(($total + $activitys) / $classCount * 100, 2);
                    $data[]=[
                        'rol' => $student->enrollment_number,
                        'name' => $student->name,
                        'from' => $from,
                        'to' => $to,
                        'classCount' => $classCount,
                        'total' => $total,
                        'per' => $percentage,
                    ];
                
            }

    }

    return collect($data); 
}
public function map($data):array{
    return[
        $data['rol'],
        $data['name'],
        $data['from'],
        $data['to'],
        $data['classCount'],
        $data['total'],
        $data['per'],
    ];
}

public function headings():array{
   return[
    'Enrollment Number',
    'Name',
    'From',
    'To',
    'Total Class',
    'Present',
    'Attendance %'
   ];
}
}
