<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use  App\Models\Student_class;
use App\Models\Subject;
use Excel;

class Teacher_mapping implements FromCollection,WithHeadings,WithMapping
{
    protected $id;
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
        return Subject::where('class_id',$this->id)->get();
    }
    public function map($subject):array{
        return[
            $subject->subject_id,
            $subject->subject_name,
        ];
    }
    public function headings():array{
       return[
        'Id',
        'Subject',
        'Professor'
       ];
    }
}
