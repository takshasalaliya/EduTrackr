<?php
namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Program;
use App\Models\Optional_subject;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Student_class;
use App\Models\Demo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teaching_hour;
use App\Models\Timetable;
use App\Models\Teaching_staff;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\professor;
use Illuminate\Support\Carbon; 
use App\Imports\StudentImport;
use App\Imports\OptionalImport;
use App\Imports\TeacherImport;
use App\Imports\SubjectImport;
use App\Imports\StudentClass;
use App\Imports\Subject_mapping;
use App\Imports\MasterImport;
use Excel;
use App\Exports\OptionalSubject;
use App\Exports\Teacher_mapping;
use App\Models\Gcode;

class AdminController extends Controller
{
 
    
    function add_teacher(Request $request){
        $request->validate([
            'name' => 'required|min:2|max:100',
            'shortname' => 'required|min:1|max:20',
            'phone' => 'required',
            'email' => 'required|email',
           
            'role' => 'required',
           
        ]);
        try{
        $valid=User::where('name',$request->name)->where('email',$request->email)->where('role',$request->role)->where('phone_number',$request->phone)->first();
    //   return $valid;
        if(!empty($valid->name)){
            return redirect()->back()->with('error','Teacher information will already added!');
            
        }
        $data= new User();
        $data->name=$request->name;
        $data->short_name=$request->shortname;
        $data->phone_number=$request->phone;
        $data->email=$request->email;
        $data->role=$request->role;
        $password=Str::random(10);
        $data->plain_password=$password;
        // return $request;
        $data->password= $password;
        
       
            $message=[
                'name'=> $request->name,
                'phone'=>$request->phone,
                'shortname'=>$request->shortname,
                'counselor'=>$request->role,
                'password'=>$password
                

            ];
        $subject="Add In Attendent Management";
            if($data->save()){
            
        

            Mail::to($request->email)->send(new professor($message,$subject));   
            return redirect()->back()->with('success','Teacher information will added!');
        }
        return redirect()->back()->with('error','Teacher information will not added!');
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }    

    function excel_teacher(Request $request){
        // return dd($request->all());
        $request->validate([
            'excel_teacher' => 'required|file|mimes:xlsx,csv,xls'
        ]);
        try{
        Excel::import(new TeacherImport,$request->file('excel_teacher'));
        return redirect()
        ->back()->with('success', 'Teacher added successfully!');   
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }

    function list_teach(){
        try{
        $data=User::all();
        $select="all";
        return view('admin/teacher_list',['teachers'=>$data,'select'=>$select]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }



    function delete_teacher($id){
        try{
        $data= User::destroy($id);
        
        if($data){
            if($data){
            return redirect()->back()->with('success','Teacher Information Deleted!');
            }
        
    }
        return redirect()->back()->with('error','Teacher Information is not Deleted!');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
    }



    function edit_teacher_success(Request $request, $id){
        $data= User::find($id);
        // return $id;
        $request->validate([
            'name' => 'required|min:2|max:100',
            'shortname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'role' => 'required',   
           
        ]);
        try{
        if($request->password){
            $password=$request->password;
        }
        else{
            $password=$data->plain_password;
        }
        $data->name=$request->name;
        $data->short_name=$request->shortname;
        $data->phone_number=$request->phone;
        $data->email=$request->email;
        $data->role=$request->role;
        $data->plain_password=$password;
        $data->password= $password;

        $message=[
            'name'=> $request->name,
            'phone'=>$request->phone,
            'shortname'=>$request->shortname,
            'counselor'=>$request->role,
            'password'=>$password,
            

        ];
    
    $subject="Your Profile is Updated";

        if($data->save()){
            Mail::to($request->email)->send(new professor($message,$subject));   
            return redirect('teacher_list')->with('success','Teacher information will added!');
        }
    
        return redirect('teacher_list')->with('error','Teacher information will not added!');
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }

    function edit_teacher($id){
        try{
        $data= User::find($id);
        
        return view('admin/edit_teacher',['teacher'=>$data]);
        
        return redirect('login')->with('error','First Login With same Conselor Account');
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }


    function teacher_list_filter(Request $request){
        try{
        if($request->filter=='all'){
            $data=User::all();
            $select="all";
        return view('admin/teacher_list',['teachers'=>$data,'select'=>$select]);
        }
        else{
            $data=User::where('role',$request->filter)->get();
            $select=$request->filter;
        return view('admin/teacher_list',['teachers'=>$data,'select'=>$select]);
                
        }
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }


//login


    function login(Request $request){
        // return $request;
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        try{
        if(Auth::attempt($credentials)){
            $role=Auth::user()->role;
            if($role=='admin'){
                return redirect('/admin');
            }
            if($role=='reader'){
                return redirect('/reader');
            }
            if($role=='counselor'){
                return redirect('/counselor');
            }
            if($role=='student'){
                return redirect('/student');
            }
        }
        return redirect()->back()->with('error','Invalid Data');
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }


    function logout(){
        try{
        Auth::logout();
        return redirect('login');
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }

//dashboard


    function dashboard(){
        try{
        $student=Student::count();
        $teacher=User::where('role','reader')->count();
        $counselor=User::where('role','counselor')->count();
        $class=Student_class::count();
        $class_name=Student_class::with('program')->get();
        return view('admin/welcome',['students'=>$student,'teachers'=>$teacher,'counselors'=>$counselor,'classes'=>$class,'class_name'=>$class_name]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }

    




//add class

function add_class(Request $request){
    $request->validate([
        'date_from' => 'required',
        'date_to' => 'required',
        'semnumber' => 'required',
        'stream' => 'required',
        'devision' => 'required',
        'counselor' => 'required',
    ]);
   

    try{

$year=$request->date_from.'-'.$request->date_to;
$data= new Student_class();
$data->stream=$request->stream;
$data->year=$year;
$data->sem=$request->semnumber;
$data->devision=$request->devision;
$data->coundelor_id=$request->counselor;


if($data->save()){
    return redirect()->back()->with('success','Class Information Added!');
}
return redirect()->back()->with('error','Class Information is not Added!');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function list_class(){
try{
$data= Student_class::with(['teacher','program'])->get();
$program=Program::all();
// return $data;
$select='all';
return view('admin/class_list',['classes'=>$data,'select'=>$select,'programs'=>$program]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}


function get_counselor(){
    try{
$data = User::where('role','counselor')->get();
$program=Program::all();
return view('admin/add_class',['datas'=>$data,'programs'=>$program]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}

function edit_class($id){
    try{
$data = Student_class::find($id);
$program=Program::all();
$class = User::where('role','counselor')->get();

return view('admin/edit_class',['datas'=>$data,'classes'=>$class,'programs'=>$program]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}

function edit_class_success(Request $request,$id){
$request->validate([
    'date_from' => 'required',
    'date_to' => 'required',
    'semnumber' => 'required',
    'stream' => 'required',
    'devision' => 'required',
    'counselor' => 'required',
]);
try{
$year=$request->date_from.'/'.$request->date_to;
$data=Student_class::find($id);
$data->stream=$request->stream;
$data->year=$year;
$data->sem=$request->semnumber;
$data->devision=$request->devision;
$data->coundelor_id=$request->counselor;
if($data->save()){
    return redirect('class_list')->with('success','Class Information Edit!');
}
return redirect()->back()->with('error','Class Information is not Edit!');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function delete_class($id){
    try{
$data = Student_class::destroy($id);
if($data){
    return redirect('class_list')->with('success','Class Information Delete!');
}
return redirect()->back()->with('error','Class Information is not Delete!');  
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function class_list_filter(Request $request){
    try{
if($request->field=='all'){
    $data= Student_class::with(['teacher','program'])->get();
    $select='all';
    $program=Program::all();
    return view('admin/class_list',['classes'=>$data,'select'=>$select,'programs'=>$program]);
}
else{
    $data= Student_class::with(['teacher','program'])->whereHas('program',function($query)use($request){
        $query->where('program_id',$request->field);
    })->get();
    // return $request;
    $select=$request->field;
    $program=Program::all();
    return view('admin/class_list',['classes'=>$data,'select'=>$select,'programs'=>$program]);
}
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

  
//add field

function field_list(){
    try{
    $data=Program::all();
    return view('admin/add_program',['datas'=>$data]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}


function add_field_success(Request $request){
    try{
    $data=new Program();
    $data->name=$request->field;
    if($data->save()){
        return redirect()->back()->with('success','Program add successfully');
    }
    return redirect()->back()->with('error','Program will not added');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}
function delete_program($id){
    // return $id;
    try{
    $data=Program::destroy($id);
    if($data){
        return redirect()->back()->with('success','Program delete successfully');
    }
    return redirect()->back()->with('error','Program will not delete');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}


   
function add_student_excel(Request $request){
    try{
    Excel::import(new StudentImport,$request->file('excel_file'));
    return redirect()
    ->back()->with('success', 'Student added successfully!');   
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
 }


    function list_stud(){
        try{
        $students= Student::with('class.program')->get();
        $class=Student_class::all();
        return view('admin/student_list',['students'=>$students,'classes'=>$class]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }


    function student_filter(Request $request){
        try{
            if($request->filter==0){
                $students= Student::with('class.program')->get();
        $class=Student_class::all();
        return view('admin/student_list',['students'=>$students,'classes'=>$class]);
            }
            $students= Student::with('class.program')->where('class_id',$request->filter)->get();
            $class=Student_class::all();
            return view('admin/student_list',['students'=>$students,'classes'=>$class]);
            }catch (\Throwable $e) {
                return redirect()->back()->with('error','Error: '.$e->getMessage());
            }
    }

    function edit_value ($id){
        try{
        $student= Student::with('class.program')->find($id);
        $class=Student_class::all();
        
        return view('admin/edit_student',['students'=>$student,'classes'=>$class]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
       
    }

    function edit_success(Request $request, $id) {

        $request->validate([
            'name' => 'required|min:3|max:100',
            'rollnumber' => 'required|min:2|max:20',
            's_phone' => 'required',
            // 's_email' => 'required|email',
            'p_phone' => 'required',
            'p_email' => 'email|required',
            'student_class' => 'required|exists:student_classes,id',
        ]);
        try{
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
            return redirect('student_list_admin')->with('success', 'Student edited successfully!');
        }
    
        return redirect('student_list_admin')->with('error', 'Student was not edited!');
    }catch (\Throwable $e) {
        if ($e->getCode() == '23000') {
            return back()->with('error', 'Duplicate enrollment number!');
        }
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }


    function delete_student($id) {
        try{
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
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }

    function search_student(Request $request){
        try{
        $students=Student::where('enrollment_number','like',"%$request->search%")->get();
        
        return view('admin/student_list',['students'=>$students]);
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
    }






//add teacher








//add class



function find_class_subject(){
    try{
    $classes=Student_class::with(['teacher','program'])->get();
    // return $classes;
    return view('admin/add_subject',['classes'=>$classes]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}



function excel_subject(Request $request){
        // $stream=Program::where('name','ITM')->first();
        // return $stream->program_id;
        //  $data=Student_class::where('stream',$stream->program_id)->where('sem',1)->first();
        //  return $data->id;
        // return dd($request->all());
        $request->validate([
            'excel_subject' => 'required|file|mimes:xlsx,csv,xls'
        ]);
        try{
        Excel::import(new SubjectImport,$request->file('excel_subject'));
        return redirect()
        ->back()->with('success', 'Subject added successfully!');   
        }catch (\Throwable $e) {
            return redirect()->back()->with('error','Error: '.$e->getMessage());
        }
}

function add_subject(Request $request){
    
    
    
    $class= new Subject();
    $request->validate([
        'name' => 'required',
        'shortname' => 'required',
        'code' => 'required',
        'class' => 'required',
        'category' => 'required',
        'l_category' => 'required',
    ]); 
    $sum=0;
    try{
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
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function list_subject(){
    try{
    $subject=Subject::with('student_class.program')->get();
    $program=Student_class::with('program')->get();
    return view('admin/list_subject',['subjects'=>$subject,'programs'=>$program]);
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}

function delete_subject($id){
    try{
    $data=Subject::destroy($id);
    if($data){
        return redirect('subject_list_admin')->with('success','Successfull Delete Subject');
    }
    return redirect('subject_list_admin')->with('error','Error Occur on Delete Subject');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function edit_subject($id){
    try{
    $datas=Subject::find($id);
    $classes=Student_class::with('program')->get();
    return view('admin/edit_subject',['subjects'=>$datas,'classes'=>$classes  ]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function edit_subject_final(Request $request,$id){
    $request->validate([
        'name' => 'required',
        'shortname' => 'required',
        'code' => 'required',
        'class' => 'required',
        'category' => 'required',
        'l_category' => 'required'
    ]); 
    try{

     $class=Subject::find($id);
    $class->subject_name=$request->name;
    $class->short_name=$request->shortname;
    $class->subject_code=$request->code;
    $class->class_id=$request->class;
    $class->category=$request->category;
    $class->lecture_category=$request->l_category;
    if($class->save()){
        return redirect('subject_list_admin')->with('success','Class Edit Successfully');
    }
    return redirect('subject_list_admin')->with('error','error will occur');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
    
}

function subject_list_filter(Request $request){

    try{
    if($request->class_filter=='all'){
        $data=Subject::with('student_class')->get();
        $program=Student_class::with('program')->get();
        return view('admin/list_subject',['subjects'=>$data,'programs'=>$program]);
    }
    elseif($request->class_filter!="all"){
        // return $request;
        $data=[];
        $program=Student_class::with('program')->get();
        $classes_for_filter_dropdown=1;
       
            if($request->field!="all" && isset($request->field)){
            // return $request;
        $data=Subject::where('category',$request->field)->with('student_class')->where('class_id',$request->class_filter)->get();
        return view('admin/list_subject',['subjects'=>$data,'programs'=>$program,'classes_for_filter_dropdown' => $classes_for_filter_dropdown]);    
        }
        if($request->class_filter!="all"){
            $data=Subject::with('student_class')->where('class_id',$request->class_filter)->get();
            return view('admin/list_subject',['subjects'=>$data,'programs'=>$program,'classes_for_filter_dropdown' => $classes_for_filter_dropdown]);    
      
        }
        
            }
    else{
        $data=Subject::with('student_class')->whereHas('student_class',function($query)use($request){
            $query->where('stream',$request->field);
        })->get();
        
        $program=Student_class::with('program')->get();
        return view('admin/list_subject',['subjects'=>$data,'programs'=>$program]);
    }
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}


//subject allocated

public function subjectallocateget(Request $request)
{
    try{
    $sem = [];
    $year = [];
    $devision = [];
    $teacher = [];
    $subject = [];
    $program=[];
    $class_id=[];
    $class_id_for_download=[];
    $program=Program::all();
    if($request->has(['program'])&& !$request->program==""){
        $sem=Student_class::where('stream',$request->program)->get();
        $sem=$sem->unique('sem');
        
    }
    if($request->has(['program','sem'])&& !$request->program=="" && !$request->sem==""){
        $year=Student_class::where('stream',$request->program)
                            ->where('sem',$request->sem)
                            ->get();
                            $year=$year->unique('year');
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


    
    return view('admin/subject_allocated', [
        
        'sem' => $sem,
        'year' => $year,
        'devision' => $devision,
        'teacher' => $teacher,
        'subject' => $subject,
        'programs'=>$program,
        'class_id' => $class_id,
    ]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}


function subjectallocatedfinal(Request $request){
    $request->validate([
        'subject' => 'required',
    ]);
    try{
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
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
    
}


function excel_teacher_subject($id){
    try{
    return Excel::download(new Teacher_mapping($id),'teacher.xlsx');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}
function subject_maping(Request $request){
 $request->validate([
        'subject_maping' => 'required|file|mimes:xlsx,csv,xls'
    ]);
    try{
    Excel::import(new Subject_mapping,$request->file('subject_maping'));
    return redirect()
    ->back()->with('success', 'Subject added successfully!');   
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}
function list_teachingstaff(){
    try{
    $teachingstaff=Teaching_staff::with(['teacher','subject.student_class.program'])->get();
    $teacher=User::all();
    $program=Student_class::with('program')->get();
    return view('admin/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'classes_for_filter_dropdown' => $program]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
    // return $teachingstaff;
}
function delete_staff($id){
try{
$data = Teaching_staff::destroy($id);

if ($data) {
    return redirect()->back()->with('success', 'Staff deleted successfully.');
}

return redirect()->back()->with('error', 'Staff deletion failed.');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}


function teachingstaff_list_filter(Request $request){
    try{
if($request->class_filter==""){
    $teachingstaff=Teaching_staff::with(['teacher','subject'])->get();
    $teacher=User::all();
    $program=Student_class::with('program')->get();
    // return $request;
    return view('admin/list_teachingstaff',['teachingstaffs'=>$teachingstaff ,'classes_for_filter_dropdown' => $program]);    }
else{
    $teacher=User::all();
    $program=Student_class::with('program')->get();
    if($request->teacher_filter!=null && isset($request->teacher_filter)){
    $teachings=Teaching_staff::with(['teacher','subject'])->where('staff_id',$request->teacher_filter)->get();
    $teachingstaff=[];
        foreach($teachings as $teach){
            if($teach->subject->class_id==$request->class_filter){
                $teachingstaff[]=$teach;
            }
        }
   return view('admin/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'teacher'=>$teacher,'classes_for_filter_dropdown' => $program]);  
  }

  if($request->class_filter!=null){
    $teachings=Teaching_staff::with(['teacher','subject'])->get();
    $teachingstaff=[];
        foreach($teachings as $teach){
            if($teach->subject->class_id==$request->class_filter){
                $teachingstaff[]=$teach;
            }
        }
   return view('admin/list_teachingstaff',['teachingstaffs'=>$teachingstaff,'teacher'=>$teacher,'classes_for_filter_dropdown' => $program]);   
 }
    
    
    }
   
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}



function optional(Request $request){
$request->validate([
    'subject'=>'required',
    'student' => 'required',
]);
try{
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
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function excel_optional(Request $request){
    try{
    $request->validate([
        'excel_optional' => 'required|file|mimes:xlsx,csv,xls'
    ]);
    Excel::import(new OptionalImport,$request->file('excel_optional'));
    return redirect()
    ->back()->with('success', 'Maping added successfully!');   
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function excel_dowload($id){
    try{
    return Excel::download(new OptionalSubject($id),'optional.xlsx');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}

}

function optionalgroup(Request $request){
    // return "f";
    try{
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
$class_id_for_download=[];
$program=$program->unique('stream');
if($request->has(['field']) && !$request->field == ""){
    $sem=Student_class::where('stream',$request->field)->get();
   $sem= $sem->unique('sem');
}
if($request->has(['field','sem']) && !$request->field == "" && !$request->sem == "" ){
    $year=Student_class::where('stream',$request->field)->where('sem',$request->sem)->get();
    $year=$year->unique('year');
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
        $class_id_for_download=$id->id;
        }
        }
        $valid=1;
       

    }

return view('admin/optional_subject_group',[
    'select' => $select,
    'sem'=>$sem,
    'valid'=>$valid,
    'year'=>$year,
    'devision' => $devision,
    'subject' => $subject,
    'students' => $student,
    'programs'=>$program,
    'class_id_for_download' => $class_id_for_download,
]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function optional_list(){
    try{
        $datashow=0;
$data=Optional_subject::with('student','subject.student_class.program')->get();
// return $data;
if($data){
    $datashow=1;
}
$classes=Student_class::with('program')->get();
return view('admin/optional_subject_list',[
    'datas' => $data,
    'classes_for_filter' => $classes,
    'datashow' => $datashow,
]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function optional_subject_list_admin(Request $request){
    try{
        $data=[];
        $sub_id=0;
        $datashow=0;
        if(!empty($request->class_filter)){
            $subjects=Optional_subject::with('student','subject.student_class.program')->get();
            // return $request;
            if(!empty($request->subject_filter)){
                foreach($subjects as $sub){
                    if($sub->subject->class_id==$request->class_filter && $sub->subject->subject_id==$request->subject_filter){
                        $data[]=$sub;
                        $sub_id=$sub->subject->class_id;
                        $datashow=1;
                    }
                }
            }else{
            foreach($subjects as $sub){
                if($sub->subject->class_id==$request->class_filter){
                    $data[]=$sub;
                    $sub_id=$sub->subject->class_id;
                    $datashow=1;
                }
            }
        }
            // return $data;
            $subject=Subject::where('class_id',$sub_id)->where('category','optional')->get();
            // return $subject;
            $classes=Student_class::with('program')->get();
            
            return view('admin/optional_subject_list',[
                'datas' => $data,
                'classes_for_filter' => $classes,
                'subjects_for_filter' => $subject,
                'datashow' => $datashow,
            ]); 
            
        }
        
        if(empty($request->class_filter) || $request->subject_filter){
            $data=Optional_subject::with('student','subject.student_class.program')->get();
// return $data;
            if($data){
                $datashow=1;
            }
            $classes=Student_class::with('program')->get();
            return view('admin/optional_subject_list',[
                'datas' => $data,
                'classes_for_filter' => $classes,
                'datashow' => $datashow,
            ]);
        }

    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
}


function delete_optional($id){
    try{
$forward=Optional_subject::with('student')->where('id',$id)->first();
$forward->student->optional='no';
$data=$forward->delete();
if($data){
$forward->student->save();
return redirect()->back()->with('success','Student optional data will deleted');
}
return redirect()->back()->with('error','Student optional data will not deleted');
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function whatsapp_message(Request $request){
    try{
    if($request->has(['device','number'])){
        $response = Http::withHeaders([
            'Authorization' => 'hzXKzv3jEyrPN1Bsu9noi3hj68d1EgAjVVGLDBbd',
        ])->post('https://api.fonnte.com/add-device', [
            'name' => $request->device,
            'device' => $request->number,
            'autoread' => true,
            'personal' => true,
            'group' => false
        ]);
         
       
    }
    
    $profile=Http::withHeaders([
        'Authorization' => 'hzXKzv3jEyrPN1Bsu9noi3hj68d1EgAjVVGLDBbd'
    ])->post('https://api.fonnte.com/get-devices',[

    ]);
    $datas=$profile->json('data');  
    // return $datas;  
    //  $url=[];
    $otp_message="no";
    $otp="ok";
    return view('admin/whatsapp_message',['datas'=>$datas,'otp_message' => $otp_message,  'otp'=>$otp,]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

function whatsapp_img($token,$number){
    // return $number;
    try{
    $img=Http::withHeaders([
        'Authorization' => $token
    ])->post('https://api.fonnte.com/qr',[
        'type' => 'qr',
        'whatsapp' => $number,
    ]);
    // return $img;
    $profile=Http::withHeaders([
        'Authorization' => 'hzXKzv3jEyrPN1Bsu9noi3hj68d1EgAjVVGLDBbd'
    ])->post('https://api.fonnte.com/get-devices',[

    ]);
    $datas=$profile->json('data');  
    $img=$img['url'];
    // return $img;
    $otp_message="no";
    $otp="no";
    return view('admin/whatsapp_message',['img'=>$img,'datas'=>$datas,'otp_message' => $otp_message, 'otp'=>$otp,]);
}catch (\Throwable $e) {
    return redirect()->back()->with('error','Error: '.$e->getMessage());
}
}

 
    function master_file(Request $request){
        try{
        $request->validate([
            'master' => 'required|file|mimes:xlsx,csv,xls'
        ]);
        // dd($request);
        Excel::import(new MasterImport,$request->file('master'));
        return redirect()
        ->back()->with('success', 'Master File Updated successfully!');   
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
     function add_class_excel_admin(Request $request){
        try{
        // return dd($request);
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv,xls'
        ]);
        // return dd($request);
        Excel::import(new StudentClass,$request->file('excel_file'));
        return redirect()
        ->back()->with('success', 'Student Class Updated successfully!');   
    }catch (\Throwable $e) {
        return redirect()->back()->with('error','Error: '.$e->getMessage());
    }
    }
      
    }
 

