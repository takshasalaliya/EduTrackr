<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Program;
use App\Models\Optional_subject;
use Illuminate\Support\Facades\Http;
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


class TeacherController extends Controller
{
    //
    
function select_attendent(){
  
    
    $subject=Teaching_staff::with(['subject.student_class.program'])->where('staff_id',Auth::user()->id)->get();
    $teacher=User::find(Auth::user()->id);
    
           return view('teacher/select_operation',[
            'subjects'=>$subject,
            'teachers'=>$teacher,
            ]);
}



function selectes_data(Request $request){
  
    if($request->subject==""){
        return redirect()->back()->with('error','select subject');
    }
    
    $request->validate([
        'date'=>'required'
    ]);
    $sum=0;
    $yes=1;
    $attendent="no";
    $optional=[];
    $datas=Teaching_staff::with(['subject.student_class.program','teacher'])->get();
    $students=Student::all();
    $subject=Teaching_staff::with('subject')->where('id',$request->subject)->first();
    $subject=$subject->subject_id;
    $data_database=Attendence::where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
   
    if (empty($data_database->leacture_no)) {
        $attendent = "yes";
    } 
    // return $attendent;
    $findoptinal=Subject::where('subject_id',$subject)->first();
    if($findoptinal->category=='optional'){
        $optional=Optional_subject::with('student')->where('subject_id',$subject)->get();
        $yes=0;
    }
    if($request->submit=='edit'){
        $sum=0;
        $subject=$request->subject;
        $attendent=Attendence::with('student')->where('staff_id',$subject)->where('leacture_no',$request->leacture)->get();
        $datas=Teaching_staff::with(['subject.student_class.program','teacher'])->get();
        return view('teacher/edit_attendent',['pervious'=>$request,'datas'=>$datas,'attendent'=>$attendent,'sum'=>$sum]);
    }
    if($request->submit=='generate'){
        $code=Str::password(5,numbers: true,symbols: false,letters: false,spaces: false);
        
      
        $checks=Gcode::where('staff_id',$request->subject)->where('lecture_no',$request->leacture)->get();
        foreach($checks as $check){
            $delete=Gcode::destroy($check->id);
        }
        $data=new Gcode();
        $data->code=$code;
        $data->staff_id=$request->subject;
        $data->lecture_no=$request->leacture;
        $data->created_at=$request->date;
        
        if($data->save()){
            if($findoptinal->category=='optional'){
                $subject=Teaching_staff::with('subject')->where('id',$request->subject)->first();
                
                $student=Student::where('class_id',$subject->subject->class_id)->get();
                $optional=Optional_subject::with('student')->where('subject_id',$subject->subject->subject_id)->get();
                foreach($optional as $data){
                    $valid=Attendence::where('student_id',$data->student_id)->where('staff_id',$request->subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
                    if(empty($valid->id)){
                    $data1=new Attendence();
                    $data1->student_id=$data->student->student_id;
                    $data1->staff_id=$request->subject;
                    $data1->leacture_no=$request->leacture;
                    $data1->created_at=$request->date;
                    $data1->attendance='absent';
                    $data1->unit=$request->unit;
                    $data1->save();
                    }
                }
            }
            else{
                $subject=Teaching_staff::with('subject')->where('id',$request->subject)->first();
                
                $student=Student::where('class_id',$subject->subject->class_id)->get();
                foreach($student as $data){
                    $valid=Attendence::where('student_id',$data->student_id)->where('staff_id',$request->subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
                    if(empty($valid->id)){
                    $data1=new Attendence();
                    $data1->student_id=$data->student_id;
                    $data1->staff_id=$request->subject;
                    $data1->leacture_no=$request->leacture;
                    $data1->created_at=$request->date;
                    $data1->attendance='absent';
                    $data1->unit=$request->unit;
                    $data1->save();
                    }
                }
            }
            return view('teacher/generate_code',[
                'code' => $code,
            ]);
        }
        
    }
    return view('teacher/main_attendent',['pervious'=>$request,'datas'=>$datas,'students'=>$students,'sum'=>$sum,'optional'=>$optional,'yes'=>$yes,'attendent'=>$attendent]);
}



function delete_code($code){
   $data=Gcode::where('code',$code)->first();
   if($data->delete()){
    return redirect('select')->with('success','Code delete Successfully');
   }
   return redirect('select')->with('error','Code delete unSuccessfully');
}

function dashboard_teacher(){
    $subject=Teaching_staff::with(['subject.student_class.program'])->where('staff_id',Auth::user()->id)->get();
        $unit=Attendence::with('teaching_staff')->get();
        $class_student=Student::all();
    return view('teacher/dashboard_teacher',[
        'subjects'=>$subject,
        'units'=>$unit,
        'class_student'=>$class_student
    ]);
}





function final_attendent(Request $request){
   

    foreach($request->student as $std){
        $valid=Attendence::where('student_id',$std)->where('staff_id',$request->staff_id)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
        
        if(empty($valid)){
       
        $data=new Attendence();
        $save=0;
        $data->student_id=$std;
        $data->staff_id=$request->staff_id;
        $data->attendance=$request->$std;
        $data->leacture_no=$request->leacture;
        $data->created_at=$request->date;
        $data->unit=$request->unit;
       $save= $data->save();
       
        if($save != 1){
            return redirect()->back()->with('error','error will occur refill attendent');
        }
    }
    }
    return redirect()->back()->with('success','successfull added attendent');
    
}

function attendent_list(Request $request){
    
    $student=[];
    $id=[];
    $valid=[];
    $subject=Teaching_staff::with(['subject.student_class.program','teacher'])->where('staff_id',Auth::user()->id)->get();
    // return $subject;
    if($request->has('subject') && $request->subject !=""){
        // $student=Student::all();
        $student=Attendence::with(['student','teaching_staff'])->get();
        // return $student;
        $id=$request->subject;
        foreach($student as $std){
            if($std->staff_id == $id){
            $valid[]=$std->student->enrollment_number;
            }
        }
        $valid= array_unique($valid);
        
    // return;      
    }
    return view('teacher/attendent_list',[
        'subject' => $subject,
        'student'=>$student,
        'id' =>$id,
        'valid' => $valid,
        'subject_id'=>$request->subject,
        
    ]);
    
}

function edit_attendendent(Request $request){
    foreach($request->student as $std){
 
      
        $data=Attendence::where('student_id',$std)->where('staff_id',$request->staff_id)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
        
        $save=0;
        
        $data->attendance=$request->$std;
      
       $save= $data->save();
       
        if($save != 1){
            return redirect()->back()->with('error','error will occur refill attendent');
        }   
    }
    return redirect()->back()->with('success','successfull added attendent');
    
    }

    function generate_pdf($id){
        // return $id;
        $student=[];
      
        $valid=[];
        $count=0;
        
            $student=Attendence::with(['student','teaching_staff'])->get();
            // return $student;
            foreach($student as $std){
                if($std->staff_id == $id){
                $valid[]=$std->student->enrollment_number;
                ++$count;
                $class=Attendence::with(['teaching_staff.subject.student_class.program'])->first();
                $teacher=Attendence::with(['teaching_staff.subject.student_class.teacher'])->first();
                $counslor=$class->teaching_staff->subject->student_class->teacher->name;
            $subject=$class->teaching_staff->subject->subject_name;
            
                }
            }
           
            $class=$class->teaching_staff->subject->student_class->program->name.'/'.$class->teaching_staff->subject->student_class->sem.'('.$class->teaching_staff->subject->student_class->devision.')';
            // return view('teacher/pdf_attendent',[
            //     'student' =>$student,
            //     'valid' => $valid,
            //     'id' => $id,
            //     'count'=>$count,
            //     'class'=>$class,
            //     'subject' =>$subject,
            //     'teacher' => $counslor
            // ]);

            $valid= array_unique($valid);
            $data=[
                'student' =>$student,
                'valid' => $valid,
                'id' => $id,
                'count'=>$count,
                'class'=>$class,
                'subject' =>$subject,
                'teacher' => $counslor
            ];
            
            
    $pdf = PDF::loadView('teacher.pdf_attendent', $data);
    return $pdf->download('attendent.pdf');
    
}

function generate_excel($id){
    return Excel::download(new ExcelTeacher($id),'attendent.xlsx');
}



function see_attendendent($code){
    $codes=Gcode::where('code',$code)->first();
    if(!empty($codes->created_at)){
    $date=$codes->created_at;
    $leacture=$codes->lecture_no;
    $staff_id=$codes->staff_id;
    $student=Attendence::with('student')->where('staff_id',$staff_id)->where('created_at',$date)->where('leacture_no',$leacture)->get();
    $duplicated=Duplicated::where('code_id',$code)->get();
    // return $duplicated  ;    
    return view('teacher/after_code_attendent',['students'=>$student,'duplicated'=>$duplicated,'code'=>$code]);
    }
    $student='no';
    $duplicated=0;
    return view('teacher/after_code_attendent',['students'=>$student,'duplicated'=>$duplicated,'code'=>$code]);
    

}
function message($id,$code,$subject){
    // return $code;
    // return $subject;
    $student=Student::where('student_id',$id)->first();
    $subject=Teaching_staff::with('subject')->where('id',$subject)->first();
    $profile=Http::withHeaders([
        'Authorization' => 'hzXKzv3jEyrPN1Bsu9noi3hj68d1EgAjVVGLDBbd'
    ])->post('https://api.fonnte.com/get-devices',[

    ]);
    $token=0;
    $datas=$profile->json('data');
    foreach($datas as $data){
        if($data['quota']>100){
            $token=$data['token'];
            break;
        }
        
    } 
    if($token==0){
        return redirect()->back()->with('error','Message Limit Is Completed Contant To Admin');

    }
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send',[
             'countryCode' => '91',
            'target' => $student->phone_number,
            'message' => 'Hi '.$student->name.' You Have '.$code.' Attendent oF This subject '.$subject->subject->subject_name,
        ]);
        if($response->json('status') ===true){
            return redirect()->back()->with('success','message send successfully');
        }
        return redirect()->back()->with('error','message will not send');

    
}
}


