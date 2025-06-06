<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CounselorController;
use App\Http\Middleware\ValidUser;
use App\Http\Middleware\ValidTeacher;
use App\Http\Middleware\ValidCounselor;
use App\Http\Middleware\ValidAdmin;
use Illuminate\Support\Facades\Http;







Route::middleware(['Admin:admin'])->group(function(){
 Route::get('/admin',([AdminController::class,'dashboard']));
Route::view('add_teacher','admin/add_teacher');
Route::post('add_teacher_admin',[AdminController::class,'add_teacher']);
Route::post('excel_teacher',[AdminController::class,'excel_teacher']);
Route::get('teacher_list',[AdminController::class,'list_teach']);
Route::get('/delete_teacher/{id}',[AdminController::class,'delete_teacher']);
Route::get('/edit_teacher/{id}',[AdminController::class,'edit_teacher']);
Route::post('/edit_teacher/{id}',[AdminController::class,'edit_teacher_success']);
Route::get('/filter_teacher_list',[AdminController::class,'teacher_list_filter']);
Route::get('add_class',[AdminController::class,'get_counselor']);
Route::post('add_class_excel_admin',[AdminController::class,'add_class_excel_admin']);
Route::post('add_class_admin',[AdminController::class,'add_class']);
Route::get('class_list',[AdminController::class,'list_class']); 
Route::get('class_list_filter',[AdminController::class,'class_list_filter']);
Route::get('edit_class/{id}',[AdminController::class,'edit_class']);
Route::post('/edit_class_success/{id}',[AdminController::class,'edit_class_success']);
Route::get('delete_class/{id}',[AdminController::class,'delete_class']);
Route::get('field',[AdminController::class,'field_list']);
Route::post('field',[AdminController::class,'add_field_success']);
Route::get('/delete_program/{id}',[AdminController::class,'delete_program']);
Route::post('/excel_subject',[AdminController::class,'excel_subject']);
Route::get('/filter_student_list',[AdminController::class,'student_filter']);

Route::post('add_student_excel_admin',[AdminController::class,'add_student_excel']);
Route::view('add_student_admin','admin/add_student');
Route::get('student_list_admin',[AdminController::class,'list_stud']);
Route::get('edit_student_admin/{id}',[AdminController::class,'edit_value']);
Route::post('/editstudent_admin/{id}',[AdminController::class,'edit_success']);
Route::get('/delete_student_admin/{id}',[AdminController::class,'delete_student']);
Route::get('/add_subject_admin',[AdminController::class,'find_class_subject']);
ROute::post('add_subject_admin',[AdminController::class,'add_subject']);  
Route::get('subject_list_admin',[AdminController::class,'list_subject']); 
Route::get('subject_list_filter_admin',[AdminController::class,'subject_list_filter']);
Route::post('/excel_subject',[AdminController::class,'excel_subject']);
Route::get('delete_subject_admin/{id}',[AdminController::class,'delete_subject']);
Route::get('/edit_subject_admin/{id}',[AdminController::class,'edit_subject']);
Route::post('/edit_subject_admin/{id}',[AdminController::class,'edit_subject_final']);
Route::get('/subjectallocated_admin', [AdminController::class, 'subjectallocateget']);
Route::post('/subjectallocated_admin',[AdminController::class,'subjectallocatedfinal']);
// Route::get('program_admin',[AdminController::class,'program']);
Route::get('teachingstaff_list_filter_admin',[AdminController::class,'teachingstaff_list_filter']);
Route::get('/list_teachingstaff_admin',[AdminController::class,'list_teachingstaff']);
Route::get('/delete_staff_admin/{id}',[AdminController::class,'delete_staff']);
// Route::get('edit_staff_admin/{id}',[AdminController::class,'edit_staff']);
// Route::post('edit_teaching_staff_admin/{id}',[AdminController::class,'edit_final_teachingstaff']);
// //optional group
Route::get('/optional_subject_list_admin',[AdminController::class,'optional_subject_list_admin'])->name('admin.subjects.for_class');
Route::post('/optionalgroup_admin',[AdminController::class,'optional']);
Route::get('/optionalgroup_admin',[AdminController::class,'optionalgroup']);
Route::post('/excel_optional',[AdminController::class,'excel_optional']);
Route::get('excel_dowload/{id}',[AdminController::class,'excel_dowload']);
Route::get('/optionallist_admin',[AdminController::class,'optional_list']);
Route::get('delete_optional_admin/{id}',[AdminController::class,'delete_optional']);
Route::get('search_student_admin',[AdminController::class,'search_student']);
Route::get('whatsapp',[AdminController::class,'whatsapp_message']);
Route::get('whatsapp_img/{token}/{number}',[AdminController::class,'whatsapp_img']);
Route::get('/otp_whatsapp/{code}',[AdminController::class,'otp_whatsapp']);
Route::get('excel_teacher_subject/{id}',[AdminController::class,'excel_teacher_subject']);
Route::post('subject_maping',[AdminController::class,'subject_maping']);
Route::post('master_file',[AdminController::class,'master_file']);
Route::view('master_file','admin.master_file');
});

Route::middleware(['Counselor:counselor'])->group(function(){

Route::get('/counselor',([CounselorController::class,'Cdashboard']));
Route::post('/add_student_excel',[CounselorController::class,'add_student_excel']);
Route::view('/add_student','counselor/add_student');
Route::get('/student_list',[CounselorController::class,'list_stud']);
Route::get('/edit_student/{id}',[CounselorController::class,'edit_value']);
Route::post('/editstudent/{id}',[CounselorController::class,'edit_success']);
Route::get('/delete_student/{id}',[CounselorController::class,'delete_student']);
Route::get('/add_subject',[CounselorController::class,'find_class_subject']);
ROute::post('add_subject',[CounselorController::class,'add_subject']);
Route::get('/subject_list',[CounselorController::class,'list_subject']); 
Route::get('/subject_list_filter',[CounselorController::class,'subject_list_filter']);
Route::get('/delete_subject/{id}',[CounselorController::class,'delete_subject']);
Route::get('/edit_subject/{id}',[CounselorController::class,'edit_subject']);
Route::post('/edit_subject/{id}',[CounselorController::class,'edit_subject_final']);
Route::get('/subjectallocated', [CounselorController::class, 'subjectallocateget']);
Route::post('/subjectallocated',[CounselorController::class,'subjectallocatedfinal']);
// Route::get('program',[CounselorController::class,'program']);
Route::get('/teachingstaff_list_filter',[CounselorController::class,'teachingstaff_list_filter']);
Route::get('/list_teachingstaff',[CounselorController::class,'list_teachingstaff']);
Route::get('/delete_staff/{id}',[CounselorController::class,'delete_staff']);
// Route::get('edit_staff/{id}',[CounselorController::class,'edit_staff']);
// Route::post('edit_teaching_staff/{id}',[CounselorController::class,'edit_final_teachingstaff']);
//optional group
Route::post('/optionalgroup',[CounselorController::class,'optional']);
Route::get('/optionalgroup',[CounselorController::class,'optionalgroup']);
Route::get('/optionallist',[CounselorController::class,'optional_list']);
Route::get('/delete_optional/{id}',[CounselorController::class,'delete_optional']);
Route::get('/search_student',[CounselorController::class,'search_student']);
Route::get('/reader',[CounselorController::class,'dashboard_teacher']);
    Route::get('/select_counselor',[CounselorController::class,'select_attendent']);
    Route::get('/selectes_data_counselor',[CounselorController::class,'selectes_data']);
    Route::post('/final_attendent_counselor',[CounselorController::class,'final_attendent']);
    Route::get('/attendent_list_counselor',[CounselorController::class,'attendent_list']);
    Route::post('/edit_attendendent_counselor',[CounselorController::class,'edit_attendendent']);
    Route::get('/generate_pdf_counselor/{id}',[CounselorController::class,'generate_pdf']);
    Route::get('/generate_excel_counselor/{id}',[CounselorController::class,'generate_excel']);
    Route::get('/delete_code_counselor/{code}',[CounselorController::class,'delete_code']);
    Route::get('see_attendendent_counselor/{code}',[CounselorController::class,'see_attendendent']);
    Route::get('send-watsapp/{id}/{code}/{subject}',[CounselorController::class,'message']);
    Route::get('classattendent',[CounselorController::class,'classattendent']);
    Route::get('class_pdf/{to}/{from}',[CounselorController::class,'class_pdf']);
Route::get('counselor/leaves/create',[CounselorController::class,'leavecreate']);
Route::post('/activity',[CounselorController::class,'activity']);
Route::get('/counselor/leaves',[CounselorController::class,'activitylist']);
Route::get('/counselor/activity/participation/delete/{id}',[CounselorController::class,'deleteactivity']);
Route::get('/filter/date',[CounselorController::class,'filterdate']);
Route::get('/generate_excel_studentclass/{to}/{from}',[CounselorController::class,'studentclassexcel']);
Route::get('/counselor/student-attendance-detail/{id}',[CounselorController::class,'student_attendance_detail']);
});

Route::middleware(['Teacher:reader'])->group(function(){
    Route::get('/reader',[TeacherController::class,'dashboard_teacher']);
    Route::get('/select',[TeacherController::class,'select_attendent']);
    Route::get('selectes_data',[TeacherController::class,'selectes_data']);
    Route::post('final_attendent',[TeacherController::class,'final_attendent']);
    Route::get('attendent_list',[TeacherController::class,'attendent_list']);
    Route::post('edit_attendendent',[TeacherController::class,'edit_attendendent']);
    Route::get('generate_pdf/{id}',[TeacherController::class,'generate_pdf']);
    Route::get('generate_excel/{id}',[TeacherController::class,'generate_excel']);
    Route::get('/delete_code/{code}',[TeacherController::class,'delete_code']);
    Route::get('see_attendendent/{code}',[TeacherController::class,'see_attendendent']);
    Route::get('send-wa/{id}/{code}/{subject}',[TeacherController::class,'message']);
   
});

Route::middleware(['User:student'])->group(function(){
    Route::get('code_enter',[UserController::class,'usersubmit']);
    Route::get('/student',[UserController::class,'userdata']);

});

Route::get('/', function () {
    return view('index'); // or any view you want as the homepage
});

Route::view('login','login');
Route::post('loginMatch',[AdminController::class,'login']);
Route::get('logout',[AdminController::class,'logout']);
// Route::view('pdfview','teacher/pdf_attendent');

