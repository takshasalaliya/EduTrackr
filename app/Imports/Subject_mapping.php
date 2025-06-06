<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Subject;
use App\Models\User;
use App\Models\Teaching_staff;

class Subject_mapping implements ToCollection,ToModel
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
        if( $this->current>1){
            $short=explode('/',$row[2]);
            foreach($short as $name){
                $user=User::where('short_name',$name)->first();
                $subject= new Teaching_staff();
                $subject->subject_id=$row[0];
                $subject->staff_id=$user->id;
                $subject->save();
            }
        }
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
}
