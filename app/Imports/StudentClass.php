<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;
use App\Models\Program;
use App\Models\Student_class;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentClass implements ToCollection,ToModel
{
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
        if($this->current>1){
            $valid=Program::where('name',$row[0])->first();
            // dd($valid);
            if(!empty($valid->program_id)){
            // dd($row);
                $valid1=User::where('short_name',$row[4])->first();
                if(!empty($valid1->id)){
                    $data=new Student_class();
                    $data->stream=$valid->program_id;
                    $data->year=$row[1];
                    $data->sem=$row[2];
                    $data->devision=$row[3];
                    $data->coundelor_id=$valid1->id;
                    $data->save();
                }
            }
        }
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
