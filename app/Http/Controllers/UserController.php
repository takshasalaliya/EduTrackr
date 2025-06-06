<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use App\Models\Optional_subject;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Student_class;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teaching_staff;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\professor;
use Illuminate\Support\Carbon; 
use App\Imports\StudentImport;
use Excel;
use  App\Models\Attendence;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExcelTeacher;
use App\Models\Gcode;
use App\Models\Duplicated;
use App\Models\Activity;


class UserController extends Controller
{
    //

    function userdata(){

        try{
        $user = Auth::user()->id;
        $data = User::where('id',$user)->first();
        $user=Student::with('class.program')->where('enrollment_number',$data->plain_password)->first();
        
        $subject=Subject::where('class_id',$user->class_id)->where('category','required')->get();
        $optionalsubject=Optional_subject::with('subject')->where('student_id',$user->student_id)->get();    
          $attend=collect();
          $finalattend=null;
          $totallecture=0;
          $totalpresent=0;
          foreach ($subject as $sub){
              $record=Teaching_staff::with(['subject','teacher'])->where('subject_id',$sub->subject_id)->get();
              $present=0;
              $all=0;
              foreach ($record as $reco){
                  $attenddata=Attendence::with('teaching_staff.teacher')->where('staff_id',$reco->id)->where('student_id',$user->student_id)->get();
                  foreach ($attenddata as $final){
                      if($final->attendance=='present'){
                          $present++;
                          $totalpresent++;
                      }
                      $all++;
                      $totallecture++;
                  }
              }
              $per=$present==0?'0': number_format(($present / $all) * 100, 2);
              $finalsub=$sub->subject_name.'@'.$present.'/'.$all.'@'.$per.'@'.$all.'@'.$sub->short_name;
               $attend=$attend->merge($finalsub);
          }
          
           foreach ($optionalsubject as $sub){
              $record=Teaching_staff::with(['subject','teacher'])->where('subject_id',$sub->subject->subject_id)->get();
              $present=0;
              $all=0;
              foreach ($record as $reco){
                  $attenddata=Attendence::with('teaching_staff.teacher')->where('staff_id',$reco->id)->where('student_id',$user->student_id)->get();
                  foreach ($attenddata as $final){
                      if($final->attendance=='present'){
                          $present++;
                          $totalpresent++;
                      }
                      $all++;
                      $totallecture++;
                  }
              }
              $per=$present==0?'0': number_format(($present / $all) * 100, 2);
              $finalsub=$sub->subject->subject_name.'@'.$present.'/'.$all.'@'.$per.'@'.$all.'@'.$sub->subject->short_name;
               $attend=$attend->merge($finalsub);
          }
          $activity=Activity::with(['student'])->where('std_id',$user->student_id)->get();
        //   return $attend;
        return view('user/enter_code',[
            'data' => $user,
            'attend' => $attend,
            'lecture' => $totallecture,
            'present' => $totalpresent,
            'activity_participation' => $activity,
        ]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
    
    
    
    
function usersubmit(Request $request){
    try{
    $ip=$request->ip();
    $yes=1;
    $check=Gcode::where('code',$request->code)->first();
    if(empty($check->code)){
        return redirect()->back()->with('error','there was no code like this sorry');
    }
       
    
    $validuser=User::where('id',Auth::user()->id)->first();
    
    $validstudent=Student::where('enrollment_number',$validuser->plain_password)->first();
    $staff=Teaching_staff::with(['subject.student_class','teacher'])->where('id',$check->staff_id)->first();
    $validip=User::all();
    foreach($validip as $userip){
        if($userip->short_name==$ip && $userip->id != Auth::user()->id){
            $name=$userip->name;
            $yes=0;
        }
    }

    if($yes){
        $check=Gcode::where('code',$request->code)->first();
    $validuser=User::where('id',Auth::user()->id)->first();
    // return $validuser;
    $validstudent=Student::where('enrollment_number',$validuser->plain_password)->first();
    $staff=Teaching_staff::with(['subject.student_class','teacher'])->where('id',$check->staff_id)->first();
   
    if($validstudent->class_id== $staff->subject->class_id){
        $attendent=Attendence::with(['teaching_staff.subject'])->where('student_id',$validstudent->student_id)->where('staff_id',$check->staff_id)->where('leacture_no',$check->lecture_no)->where('created_at',$check->created_at)->first();
        if(!empty($attendent->student_id)){
        $attendent->attendance='present';
        $attendent->save();
        $validuser->short_name= $ip;
        $validuser->save();
        }
        else{
        return redirect()->back()->with('error','You are not in this class');
        }
        
        return redirect()->back()->with('success','Your attendent will added of the '.$attendent->teaching_staff->subject->subject_name);
    }
    else{
        return redirect()->back()->with('error','You are not in this class');
        }
    }else{

        $id=Auth::user()->name;
        $data=new Duplicated();
        $data->in_class=$name;
        $data->out_class=$id;
        $data->code_id=$request->code;
        $data->save();
        return redirect()->back()->with('error','You login with duplicated account');
    }

}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
   
}


}
