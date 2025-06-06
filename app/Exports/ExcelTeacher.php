<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use  App\Models\Attendence;
use Excel;
use Carbon\Carbon;
class ExcelTeacher implements FromCollection,WithHeadings,WithMapping
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
        $this->attendent = Attendence::with(['student', 'teaching_staff.subject'])
            ->where('staff_id', $this->id)
            ->get()
            ->groupBy('student_id')
            ->map(function ($studentData) {
                $student = $studentData->first()->student;

                // Count unique lectures per unit
                $unitPresentLectures = [];
                for ($i = 1; $i <= 6; $i++) {
                    $unitPresentLectures["unit_$i"] = $studentData
                        ->where('unit', $i)
                        ->where('attendance', 'present')
                        ->unique('leacture_no')
                        ->count();
                }

                // Total present lectures = Sum of unit-wise present lectures
                $totalPresent = array_sum($unitPresentLectures);
                $totalClasses = $studentData->unique(['leacture_no', 'unit'])->count();

                return [
                    'student' => $student,
                    'from_date' => $studentData->min('created_at'),
                    'to_date' => $studentData->max('created_at'),
                    'total_classes' => $totalClasses,
                    'present' => $totalPresent,
                ] + $unitPresentLectures;
            });

        return $this->attendent->values();
    }

    public function map($student): array
    {
        $totalClass = $student['total_classes'];
        $totalPresent = $student['present'];
        $sum = $totalClass > 0 ? ($totalPresent / $totalClass) * 100 : 0;
        $sum = number_format($sum, 2) . '%';

        return [
            $student['student']->enrollment_number,
            $student['student']->name,
            Carbon::parse($student['from_date'])->format('Y-m-d'),
            Carbon::parse($student['to_date'])->format('Y-m-d'),
            $totalClass,
            $totalPresent,
            $sum,
            $student['unit_1'], // Present lectures in Unit 1
            $student['unit_2'],
            $student['unit_3'],
            $student['unit_4'],
            $student['unit_5'],
            $student['unit_6'],
        ];
    }

    public function headings(): array
    {
        return [
            'Enrollment Number',
            'Name',
            'From Date',
            'To Date',
            'Total Lectures',
            'Total Present Lectures',
            'Percentage',
            'Present in Unit 1',
            'Present in Unit 2',
            'Present in Unit 3',
            'Present in Unit 4',
            'Present in Unit 5',
            'Present in Unit 6',
        ];
    }
}
