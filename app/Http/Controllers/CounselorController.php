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
use App\Mail\Attendantmail;
use Illuminate\Support\Carbon; 
use App\Imports\StudentImport;
use Excel;
use  App\Models\Attendence;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ExcelTeacher;
use App\Exports\StudentClass;
use App\Models\Gcode;
use App\Models\Duplicated;
use App\Models\Activity;

class CounselorController extends Controller
{
    //student

    function add_student_excel(Request $request){
        try{
            Excel::import(new StudentImport,$request->file('excel_file'));
            return redirect()->back()->with('success', 'Student added successfully!');   
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function list_stud(){
        try {
            $students = Student::with('class.program')->get();
            return view('counselor/student_list', ['students' => $students]);
       } catch (\Throwable $e) {
           return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    function edit_value ($id){
        try {
            $student= Student::with('class.program')->find($id);
            $class=Student_class::all();
            return view('counselor/edit_student',['students'=>$student,'classes'=>$class]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function edit_success(Request $request, $id) {
        try {
            $request->validate([
                'name' => 'required|min:3|max:100',
                'rollnumber' => 'required|min:1|max:20',
                's_phone' => 'required|digits_between:10,13',
                'p_phone' => 'required|digits_between:10,13',
                'p_email' => 'email|required',
                'student_class' => 'required|exists:student_classes,id',
            ]);
            $student=Student::find($id);
            $student->name=$request->name;
            $student->enrollment_number=$request->rollnumber;  
            $student->phone_number=$request->s_phone;
            $student->parents_phone_number=$request->p_phone;
            $student->parents_email=$request->p_email;
            $student->class_id =$request->student_class;
            $userdata=User::where('email',$student->email)->first();
            $userdata->name=$request->name;
            $userdata->short_name=$request->rollnumber;
            $userdata->phone_number=$request->s_phone;
            $userdata->plain_password=$request->rollnumber;
            $userdata->password=$request->rollnumber;
            $student->save();
            if ($userdata->save()) {
                return redirect('student_list')->with('success', 'Student edited successfully!');
            }
            return redirect('student_list')->with('error', 'Student was not edited!');
        } catch (\Throwable $e) {
            return redirect('student_list')->with('error', 'Error: '.$e->getMessage());
        }
    }

    function delete_student($id) {
        try {
            $data = Student::find($id);
            if(empty($data)){
                return redirect()->back()->with('error', 'mulltiple request will be sent to delete student!');
            }
            $userdata=User::where('email',$data->email)->where('plain_password',$data->enrollment_number)->first();
            $userdata->delete();
            if ($data->delete()) {
                return redirect()->back()->with('success', 'Student deleted successfully!');
            }
            return redirect()->back()->with('error', 'Failed to delete student!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    function search_student(Request $request){
        try {
            $students=Student::where('enrollment_number','like',"%$request->search%")->get();
            return view('counselor/student_list',['students'=>$students]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function Cdashboard(){
        try {
            $student=Student::count();
            $teacher=User::where('role','reader')->count();
            $counselor=User::where('role','counselor')->count();
            $class=Student_class::count();
            $class_name=Student_class::with(['teacher','program'])->get();
            $subject=Teaching_staff::with(['subject.student_class.program'])->where('staff_id',Auth::user()->id)->get();
            $unit=Attendence::with('teaching_staff')->get();
            $class_student=Student::all();
            return view('counselor/CDasboard',['class_student'=>$class_student,'students'=>$student,'teachers'=>$teacher,'counselors'=>$counselor,'classes'=>$class,'class_name'=>$class_name,'subjects'=>$subject,'units'=>$unit]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function find_class_subject(){
        try {
            $classes=Student_class::with(['teacher','program'])->get();
            return view('counselor/add_subject',['classes'=>$classes]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function add_subject(Request $request){
        try {
            $class= new Subject();
            $request->validate([
                'name' => 'required',
                'shortname' => 'required',
                'code' => 'required',
                'class' => 'required',
                'category' => 'required',
                'l_category' => 'required'
            ]); 
            $sum=0;
            foreach($request->class as $class){
                $id[$sum]=intval($class);
                $sum++;
            }
            foreach($id as $id){
                $valid=Subject::where('subject_code',$request->code)->where('class_id',$id)->first();
                if(empty($valid)){
                    $class= new Subject();
                    $class->subject_name=$request->name;
                    $class->short_name=$request->shortname;
                    $class->subject_code=$request->code;
                    $class->category=$request->category;
                    $class->class_id=$id;
                    $class->lecture_category=$request->l_category;
                    $class->save();
                    if(!$class->save()){
                        return redirect()->back()->with('error','error will get occur on this class'.$id);
                    }
                }
            }
            if($class->save()){
                return redirect()->back()->with('success','New Class Added Successfully');
            }
            return redirect()->back()->with('error','error will get occur');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function list_subject(){
        try {
            $subject=Subject::with('student_class.program')->get();
            $select="all";
            $program=Student_class::with('program')->get();
            return view('counselor/list_subject',['subjects'=>$subject,'select'=>$select,'programs'=>$program]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function delete_subject($id){
        try {
            $data=Subject::destroy($id);
            if($data){
                return redirect('subject_list')->with('success','Successfull Delete Subject');
            }
            return redirect('subject_list')->with('error','Error Occur on Delete Subject');
        } catch (\Throwable $e) {
            return redirect('subject_list')->with('error','Error: '.$e->getMessage());
        }
    }

    function edit_subject($id){
        try {
            $datas=Subject::find($id);
            $classes=Student_class::with('program')->get();
            return view('counselor/edit_subject',['subjects'=>$datas,'classes'=>$classes  ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function edit_subject_final(Request $request,$id){
        try {
            $request->validate([
                'name' => 'required',
                'shortname' => 'required',
                'code' => 'required',
                'class' => 'required',
                'category' => 'required',
                'l_category' => 'required'
            ]); 
            $class=Subject::find($id);
            $class->subject_name=$request->name;
            $class->short_name=$request->shortname;
            $class->subject_code=$request->code;
            $class->class_id=$request->class;
            $class->category=$request->category;
            $class->lecture_category=$request->l_category;
            if($class->save()){
                return redirect('subject_list')->with('success','Class Edit Successfully');
            }
            return redirect('subject_list')->with('error','error will occur');
        } catch (\Throwable $e) {
            return redirect('subject_list')->with('error','Error: '.$e->getMessage());
        }
    }

    function subject_list_filter(Request $request){
        try {
            if($request->field=='all'){
                $data=Subject::with('student_class')->get();
                $select="all";
                $program=Student_class::with('program')->get();
                return view('counselor/list_subject',['subjects'=>$data,'select'=>$select,'programs'=>$program]);
            }
            else{
                $data=Subject::with('student_class')->whereHas('student_class',function($query)use($request){
                    $query->where('stream',$request->field);
                })->get();
                $program=Student_class::with('program')->get();
                $select=$request->field;
                return view('counselor/list_subject',['subjects'=>$data,'select'=>$select,'programs'=>$program]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    public function subjectallocateget(Request $request)
    {
        try {
            $sem = [];
            $year = [];
            $devision = [];
            $teacher = [];
            $subject = [];
            $program=[];
            $class_id=[];

            $program=Student_class::with('program')->get();
            if($request->has(['program'])&& !$request->program==""){
                $sem=Student_class::where('stream',$request->program)->get();
            }
            if($request->has(['program','sem'])&& !$request->program=="" && !$request->sem==""){
                $year=Student_class::where('stream',$request->program)
                                    ->where('sem',$request->sem)
                                    ->get();
            }
            if($request->has(['program','sem','year'])&& !$request->program=="" && !$request->sem=="" && !$request->year==""){
                $devision=Student_class::where('stream',$request->program)
                                               ->where('sem',$request->sem)
                                               ->where('year',$request->year)
                                               ->get();
            }
            if($request->has(['program','sem','year','devision'])&& !$request->program=="" && !$request->sem=="" && !$request->year=="" && !$request->devision==""){
                $teacher=User::all();
            }
            if($request->has(['program','sem','year','devision','teacher'])&& !$request->program=="" && !$request->sem=="" && !$request->year=="" && !$request->devision=="" && !$request->teacher==""){
                $class_id=Student_class::where('stream',$request->program)
                ->where('sem',$request->sem)
                ->where('year',$request->year)
                ->where('devision',$request->devision)
                ->first();
                if(!empty($class_id)){
                    $class_id=$class_id->id;
                    $subject=Subject::all();
                }
            }
            return view('counselor/subject_allocated', [
                'sem' => $sem,
                'year' => $year,
                'devision' => $devision,
                'teacher' => $teacher,
                'subject' => $subject,
                'programs'=>$program,
                'class_id' => $class_id,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function subjectallocatedfinal(Request $request){
        try {
            $request->validate([
                'subject' => 'required',
            ]);
            foreach($request->subject as $subject){
                $valid=Teaching_staff::where('subject_id',$subject)->where('staff_id',$request->teacher)->first();
                if(empty($valid)){
                    $data=new Teaching_staff();
                    $data->subject_id=$subject;
                    $data->staff_id=$request->teacher;
                    if(!$data->save()){
                        return redirect()->back()->with('error','error will occur try after some time');
                    }
                }
            }
            return redirect()->back()->with('success','subject will linked successfully');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function list_teachingstaff(){
        try {
            $teachingstaff=Teaching_staff::with(['teacher','subject.student_class'])->get();
            $selete="all";
            $teacher=User::all();
            return view('counselor/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'select'=>$selete,'teacher'=>$teacher]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function delete_staff($id){
        try {
            $data = Teaching_staff::destroy($id);
            if ($data) {
                return redirect()->back()->with('success', 'Staff deleted successfully.');
            }
            return redirect()->back()->with('error', 'Staff deletion failed.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function teachingstaff_list_filter(Request $request){
        try {
            if($request->teacher=="all"){
                $selete="all";
                $teachingstaff=Teaching_staff::with(['teacher','subject'])->get();
                $teacher=User::all();
                return view('counselor/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'select'=>$selete,'teacher'=>$teacher]);
            } else {
                $teachingstaff=Teaching_staff::with(['teacher','subject'])->where('staff_id',$request->teacher)->get();
                $teacher=User::all();
                $selete=$request->teacher;
                return view('counselor/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'select'=>$selete,'teacher'=>$teacher]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function optional(Request $request){
        try {
            $request->validate([
                'subject'=>'required',
                'student' => 'required',
            ]);
            $sum1=0;
            $sum2=0;
            foreach($request->subject as $sub){
                $subject[$sum1]= $sub;
                $sum1++;
            }
            foreach($request->student as $std){
                $student[$sum2]=$std;
                $sum2++;
            }
            foreach($subject as $sub){
                foreach($student as $std){
                    $find=Optional_subject::where('student_id',$std)->where('subject_id',$sub)->first();
                    if(empty($find)){
                        $data= new Optional_subject();
                        $data->student_id=$std;
                        $data->subject_id=$sub;
                        $data->save();
                        if (!$data->save()) {
                            return redirect()->back()->with('error', 'error will occur when will student adding ');
                        }
                        $id=Student::where('student_id',$std)->first();
                        $id->optional='yes';
                        $id->save();
                        if (!$id->save()) {
                            return redirect()->back()->with('error', 'error will occur when will student adding ');
                        }
                    }
                }
            }
            return redirect()->back()->with('success', 'Successfully student onptional subject added');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function optionalgroup(Request $request){
        try {
            $select=$request->field;
            $sem=[];
            $valid=[];
            $year=[];
            $devision=[];
            $subject=[];
            $id=[];
            $student=[];
            $optional=[];
            $program=Student_class::with('program')->get();
            if($request->has(['field']) && !$request->field == ""){
                $sem=Student_class::where('stream',$request->field)->get();
            }
            if($request->has(['field','sem']) && !$request->field == "" && !$request->sem == "" ){
                $year=Student_class::where('stream',$request->field)->where('sem',$request->sem)->get();
            }
            if($request->has(['field','sem','year'])  && !$request->field == "" && !$request->sem == ""  && !$request->year == ""){
                $devision=Student_class::where('stream',$request->field)->where('sem',$request->sem)->where('year',$request->year)->get();
            }
            if($request->has(['field','sem','year','devision'])  && !$request->field == "" && !$request->sem == ""  && !$request->year == "" && !$request->devision == "" ){
                if(!$request->sem==""&&!$request->field==""&&!$request->year==""&&!$request->devision==""){
                    $id=Student_class::where('stream',$request->field)->where('sem',$request->sem)->where('year',$request->year)->where('devision',$request->devision)->first();
                    if(!empty($id)){
                        $subject=Subject::where('class_id',$id->id)->where('category','optional')->get();
                        $student=Student::where('class_id',$id->id)->get();
                    }
                }
                $valid=1;
            }
            return view('counselor/optional_subject_group',[
                'select' => $select,
                'sem'=>$sem,
                'valid'=>$valid,
                'year'=>$year,
                'devision' => $devision,
                'subject' => $subject,
                'students' => $student,
                'programs'=>$program,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function optional_list(){
        try {
            $data=Optional_subject::with('student','subject.student_class.program')->get();
            $datas=[];
            $subjects=[];
            $classes=Student_class::where('coundelor_id',Auth::user()->id)->get();
            foreach($classes as $class){
                foreach($data as $std){
                    if($std->student->class_id==$class->id){
                        $datas[]=$std;
                    }
                }

                $subject=Subject::where('class_id',$class->id)->where('category','optional')->get();
                $subjects[]=$subject;
            }

            // return $datas;
            // return $subjects;
            return view('counselor/optional_subject_list',[
                'datas' => $datas,
                'subjects_for_filter' => $subjects,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }


    function filter_optionalsuject(Request $request){
        try{
            if($request->filter!=""){
             $data=Optional_subject::with('student','subject.student_class.program')->where('subject_id',$request->filter)->get();
            }else{
             $data=Optional_subject::with('student','subject.student_class.program')->get();
            }
            $datas=[];
            $subjects=[];
            $classes=Student_class::where('coundelor_id',Auth::user()->id)->get();
            foreach($classes as $class){
                foreach($data as $std){
                    if($std->student->class_id==$class->id){
                        $datas[]=$std;
                    }
                }

                $subject=Subject::where('class_id',$class->id)->where('category','optional')->get();
                $subjects[]=$subject;
            }

            // return $datas;
            // return $subjects;
            return view('counselor/optional_subject_list',[
                'datas' => $datas,
                'subjects_for_filter' => $subjects,
            ]);

        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function delete_optional($id){
        try {
            $forward=Optional_subject::with('student')->where('id',$id)->first();
            $forward->student->optional='no';
            $data=$forward->delete();
            if($data){
                $forward->student->save();
                return redirect()->back()->with('success','Student optional data will deleted');
            }
            return redirect()->back()->with('error','Student optional data will not deleted');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function select_attendent(){
        try {
            $subject=Teaching_staff::with(['subject.student_class.program'])->where('staff_id',Auth::user()->id)->get();
            $teacher=User::find(Auth::user()->id);
            return view('counselor/select_operation',[
                'subjects'=>$subject,
                'teachers'=>$teacher,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function selectes_data(Request $request){
        try {
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
            $subject=Teaching_staff::where('id',$request->subject)->first();
            $subject=$subject->subject_id;
            $data_database=Attendence::where('staff_id',$request->subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
            if (empty($data_database->leacture_no)) {
                $attendent = "yes";
            } 
            $data_secound_class_id=Teaching_staff::with('subject')->where('id',$request->subject)->first();
            $data_secound_attendent=Attendence::with('student')->where('leacture_no',$request->leacture)->where('created_at',$request->date)->get();
            foreach ($data_secound_attendent as $secound_att){
                if($data_secound_class_id->subject->class_id==$secound_att->student->class_id){
                    $attendent='no';
                }
            }
            $findoptinal=Subject::where('subject_id',$subject)->first();
            if($findoptinal->category=='optional'){
                $optional=Optional_subject::with('student')->where('subject_id',$subject)->get();
                $yes=0;
            }
            if($request->submit=='edit'){
                $sum=0;
                $subject=$request->subject;
                $attendent=Attendence::with('student')->where('staff_id',$subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->where('unit',$request->unit)->get();
                $datas=Teaching_staff::with(['subject.student_class.program','teacher'])->get();
                return view('counselor/edit_attendent',['pervious'=>$request,'datas'=>$datas,'attendent'=>$attendent,'sum'=>$sum]);
            }
            if($request->submit=='generate'){
                $code=Str::password(5,numbers: true,symbols: false,letters: false,spaces: false);
                if($attendent=='no'){
                    return view('counselor/generate_code',[
                        'code' => $code,
                        'valid' => 'no',
                        'attend' => $attendent,
                    ]);
                }
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
                            $valid=Attendence::where('student_id',$data->student->student_id)->where('staff_id',$request->subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
                            if(!empty($valid->id)){
                                return view('counselor/generate_code',[
                                    'code' => $code,
                                    'valid' => 'no',
                                    'attend' => $attendent,
                                ]);
                            }
                        }
                        foreach($optional as $data){
                            $valid=Attendence::where('student_id',$data->student->student_id)->where('staff_id',$request->subject)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
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
                            if(!empty($valid->id)){
                                return view('counselor/generate_code',[
                                    'code' => $code,
                                    'valid' => 'no',
                                    'attend' => $attendent,
                                ]);
                            }
                        }
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
                    return view('counselor/generate_code',[
                        'code' => $code,
                        'valid' => 'yes',
                        'attend' => $attendent
                    ]);
                }
            }
            return view('counselor/main_attendent',['pervious'=>$request,'datas'=>$datas,'students'=>$students,'sum'=>$sum,'optional'=>$optional,'yes'=>$yes,'attendent'=>$attendent]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function delete_code($code){
        try {
            $data=Gcode::where('code',$code)->first();
            if($data->delete()){
                return redirect('select_counselor')->with('success','Code delete Successfully');
            }
            return redirect('select_counselor')->with('error','Code delete unSuccessfully');
        } catch (\Throwable $e) {
            return redirect('select_counselor')->with('error','Error: '.$e->getMessage());
        }
    }

    function final_attendent(Request $request){
        try {
            foreach($request->student as $std){
                $valid=Attendence::where('student_id',$std)->where('staff_id',$request->staff_id)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
                if(empty($valid)){
                    $data=new Attendence();
                    $save=0;
                    $data->student_id=$std;
                    $data->staff_id=$request->staff_id;
                    $data->attendance=$request->attendance_status[$std]==NULL?'absent':$request->attendance_status[$std];
                    $data->leacture_no=$request->leacture;
                    $data->created_at=$request->date;
                    $data->unit=$request->unit;
                    $save= $data->save();
                    if($save != 1){
                        return redirect()->back()->with('error','error will occur refill attendent');
                    }
                }
            }
            return redirect()->back()->with('success','Attendent Successfully Add');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function attendent_list(Request $request){
        try {
            $student=[];
            $id=[];
            $valid=[];
            $subject=Teaching_staff::with(['subject.student_class.program','teacher'])->where('staff_id',Auth::user()->id)->get();
            if($request->has('subject') && $request->subject !=""){
                $student=Attendence::with(['student','teaching_staff'])->get();
                $id=$request->subject;
                foreach($student as $std){
                    if($std->staff_id == $id){
                        $valid[]=$std->student->enrollment_number;
                    }
                }
                $valid= array_unique($valid);
            }


            return view('counselor/attendent_list',[
                'subject' => $subject,
                'student'=>$student,
                'id' =>$id,
                'valid' => $valid,
                'subject_id'=>$request->subject,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function edit_attendendent(Request $request){
        try {
            foreach($request->student_ids as $std){
                $data=Attendence::where('student_id',$std)->where('staff_id',$request->staff_id)->where('leacture_no',$request->leacture)->where('created_at',$request->date)->first();
                $save=0;
                $data->attendance=$request->attendance_status[$std];
                $save= $data->save();
                if($save != 1){
                    return redirect()->back()->with('error','error will occur refill attendent');
                }   
            }
            return redirect()->back()->with('success','successfull added attendent');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function generate_pdf($id){
        try {
            $student=[];
            $valid=[];
            $count=0;
            $class = null;
            $subject = null;
            $counslor = null;
            $student=Attendence::with(['student','teaching_staff'])->get();
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
            if ($class) {
                $class=$class->teaching_staff->subject->student_class->program->name.'/'.$class->teaching_staff->subject->student_class->sem.'('.$class->teaching_staff->subject->student_class->devision.')';
            } else {
                $class = '';
            }
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
            $pdf = PDF::loadView('counselor.pdf_attendent', $data);
            return $pdf->download('attendent.pdf');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function generate_excel($id){
        try {
            return Excel::download(new ExcelTeacher($id),'attendent.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }


    function studentclassexcel($to,$from){
        try {
            return Excel::download(new StudentClass($to,$from),'attendent.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function see_attendendent($code){
        try {
            $codes=Gcode::where('code',$code)->first();
            if(!empty($codes->created_at)){
                $date=$codes->created_at;
                $leacture=$codes->lecture_no;
                $staff_id=$codes->staff_id;
                $student=Attendence::with('student')->where('staff_id',$staff_id)->where('created_at',$date)->where('leacture_no',$leacture)->get();
                $duplicated=Duplicated::where('code_id',$code)->get();
                return view('counselor/after_code_attendent',['students'=>$student,'duplicated'=>$duplicated,'code'=>$code]);
            }
            $student='no';
            $duplicated=0;
            return view('counselor/after_code_attendent',['students'=>$student,'duplicated'=>$duplicated,'code'=>$code]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function message($id,$code,$subject){
        try {
            $student=Student::where('student_id',$id)->first();
            $subject=Teaching_staff::with('subject')->where('id',$subject)->first();
            $profile=Http::withHeaders([
                'Authorization' => 'hzXKzv3jEyrPN1Bsu9noi3hj68d1EgAjVVGLDBbd'
            ])->post('https://api.fonnte.com/get-devices',[]);
            $token=0;
            $datas=$profile->json('data');
            foreach($datas as $data){
                if($data['quota']>100 ){
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
                'message' => 'Hi '.$student->name.' You Have '.$code.' Attendent of this subject '.$subject->subject->subject_name,
            ]);
            if($response->json('status') ===true){
                return redirect()->back()->with('success','message send successfully');
            }
            return redirect()->back()->with('error','message will not send');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function classattendent(){
        try {
                $data=[];
                $class=Student_class::where('coundelor_id',Auth::user()->id)->get();
                foreach($class as $class){
                    $class->student=Student::where('class_id',$class->id)->get();
                    foreach($class->student as $student){
                        $attendent=Attendence::where('student_id',$student->student_id)->get();
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
                        $from = Carbon::parse($date[0])->toDateString();        // "2025-06-01"
$to = Carbon::parse(end($date))->toDateString();        // "2025-06-05"
// return $from;
$activity = Activity::where('std_id', $student->student_id)
->whereDate('from_date', '>=', $from)
->whereDate('to_date', '<=', $to)
->get();   
// return $activity;
                        $activitys=0;
                    foreach($activity as $act){
                        $activitys+=$act->session;
                    }
                    // return $activitys;
                    $percentage = ($classCount == 0) ? '0' : number_format(($total + $activitys) / $classCount * 100, 2);

                        $data[]=$student->enrollment_number.'&'.$student->name.'&'.$from.'&'.$to.'&'.$classCount.'&'.$total.'&'.$percentage;
                    }
                }
            return view('counselor/classattendent',[
                'datas'=>$data,
                'to' => $to=0,
                'from' => $from=0,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }


    function filterdate(Request $request){
        try {
            $fromDate=$request->date_from;
            $toDate=$request->date_to;
            $data=[];
            $class=Student_class::where('coundelor_id',Auth::user()->id)->get();
            foreach($class as $class){
                $class->student=Student::where('class_id',$class->id)->get();
                foreach($class->student as $student){
                    $attendent=Attendence::where('student_id',$student->student_id)->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->get();
                    // return $attendent;
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
                    $activity = Activity::where('std_id', $student->student_id)
                    ->whereDate('from_date', '>=', $fromDate)
                    ->whereDate('to_date', '<=', $toDate)
                    ->get();                
                    // return $activity;
                    $activitys=0;
                    foreach($activity as $act){
                        $activitys+=$act->session;
                    }
                    // return $activitys;
                    // return $total; 
                    $percentage = ($classCount == 0) ? '0' : number_format(($total + $activitys) / $classCount * 100, 2);
                    // return $percentage;
                    $data[]=$student->enrollment_number.'&'.$student->name.'&'.$from[0].'&'.$to[0].'&'.$classCount.'&'.$total.'&'.$percentage;
                }
            }
            return view('counselor/classattendent',[
                'datas'=>$data,
                'to' => $toDate,
                'from' =>$fromDate,
            ]);
        } catch (\Throwable $e) {
             if($e->getCode()==0){
            return redirect()->back()->with('error','Error: No Attendent Data');
            }
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }
    
    
    function class_pdf($toss,$fromss){
        try {
            
            $data=[];
            $totalstudent=0;
            $student_class = '';
            $class=Student_class::with('program')->where('coundelor_id',Auth::user()->id)->get();
            foreach($class as $classItem){
                $classItem->student=Student::where('class_id',$classItem->id)->get();
                foreach($classItem->student as $student){
                    $attendent=[];
                    $totalstudent++;
                    if($toss!=0){
                        $attendent=Attendence::where('student_id',$student->student_id)->whereBetween('created_at', [$fromss . ' 00:00:00', $toss . ' 23:59:59'])->get();
                    }else{
                        $attendent=Attendence::where('student_id',$student->student_id)->get();
                    }
                    $attendentas=Attendence::where('student_id',$student->student_id)->first();
                    if(empty($attendentas)){
                        continue;
                    }
                    $total=0;
                    $classes=0;
                    $date=[];
                    foreach($attendent as $atten){
                        $classes++;
                        $date[]=$atten->created_at;
                        if($atten->attendance=='present'){
                            $total++;
                        }
                    }
                    sort($date);
                    $from=explode(' ',$date[0]);
                    $to=explode(' ',end($date));
                    $activity = Activity::where('std_id', $student->student_id)
                    ->whereDate('from_date', '>=', $fromss)
                    ->whereDate('to_date', '<=', $toss)
                    ->get();   
                    $activitys=0;
                    foreach($activity as $act){
                        $activitys+=$act->session;
                    }
                    // return $activitys;
                    $percentage = ($classCount == 0) ? '0' : number_format(($total + $activitys) / $classCount * 100, 2);
                    $data[]=$student->enrollment_number.'&'.$student->name.'&'.$from[0].'&'.$to[0].'&'.$classes.'&'.$total.'&'.$percentage; 
                }
                $student_class=$classItem->program->name.'/'.$classItem->sem.'('.$classItem->devision.')';
            }
            $dataArr=[
                'student_class'=>$student_class,
                'datas'=>$data,
                'count'=>$totalstudent,
            ];
            $pdf = PDF::loadView('counselor.class_pdf', $dataArr);
            return $pdf->download('class.pdf');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    public function leavecreate(){
        try {
            $students=Student::all();
            return view('counselor.activityform',[
                'students' => $students,    
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    public function activity(Request $request){
        $request->validate([
            'activity_name' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
            'session_number' => 'required',
            'selected_students' => 'required',
        ]);
        try{
            foreach($request->selected_students as $std){
                $data=new Activity();
                $data->name=$request->activity_name;
                $data->teach_id=Auth::user()->id;
                $data->session=$request->session_number;
                $data->from_date=$request->date_from;
                $data->to_date=$request->date_to;
                $data->std_id=$std;
                $data->save();
            }
            return redirect()->back()->with('success','Activity Add Sucessfully.');
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    public function activitylist(){
        try{
            $datas=Activity::where('teach_id',Auth::user()->id)->get();
            return view('counselor.activitylist',[
                'activity_logs' => $datas,
            ]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    public function deleteactivity($id){
        try{
            $data=Activity::where('id',$id)->first();
            if($data->delete()){
                return redirect()->back()->with('success','Deleted Successfully');
            }
            return redirect()->back()->with('error','Error: Unable to delete activity');
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    function student_attendance_detail($id){
        try{
            $user=Student::with('class.program')->where('enrollment_number',$id)->first();
            
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
            // return $user;
            return view('counselor/userattendent',[
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

    function student_detailemail($id){
      try{
        $link = "http://127.0.0.1:8000/student-detail/".$id;
        $message = [
            'message' => $link,
        ];


        $user=Student::where('student_id',$id)->first();
    $subject="Attendance Update & Important Reminder";

    $mail=Mail::to($user->parents_email)->send(new Attendantmail($message,$subject));   
    return redirect()->back()->with('success','Email Send Successfully');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
    }
}
