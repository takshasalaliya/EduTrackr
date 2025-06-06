<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\professor;


class TeacherImport implements ToCollection,ToModel
{
    private $current=0;
    /**
    * @param Collection $collection
    * 
    */
    public function collection(Collection $collection)
    {
        //
        
    }
    
    public function model(array $row){
        try{

        $this->current++;
        if($this->current>1){
            $data= new User();
            $data->name=$row[0];
            $data->short_name=$row[1];
            $data->phone_number=$row[2];
            $data->email=$row[3];
            $data->role=$row[4];
            $password=Str::random(10);
            $data->plain_password=$password;
            $data->password= $password;
            $message=[
                'name'=> $row[0],
                'phone'=>$row[2],
                'shortname'=>$row[1],
                'counselor'=>$row[4],
                'password'=>$password
            ];
           $subject="Add In Attendent Management";
           $email=$row[3];
            if($data->save()){
                Mail::to($email)->send(new professor($message,$subject));
            }
        }

    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }

}
