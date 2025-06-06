<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use  App\Models\Student_class;
use App\Models\Student;
use Excel;
use App\Models\Subject;

class OptionalSubject implements FromCollection,WithHeadings,WithMapping
{
    protected $id;
    protected $attendent;
    public function __construct($id)
    {
        $this->id = $id;
        
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        $data=Student_class::where('id',$this->id)->first();
        return Student::where('class_id',$data->id)->get();
    }
    public function map($student):array{
        return[
            $student->enrollment_number,
            $student->name,
        ];
    }

    public function headings():array{
        $data=Student_class::where('id',$this->id)->first();
        $subject=Subject::where('class_id',$data->id)->where('category','optional')->get();

        $heading=[
            'Enrollment Number',
            'Name',
        ];

        foreach($subject as $sub){
            $heading[]=$sub->subject_name;
        }
        return $heading;
    }
}
