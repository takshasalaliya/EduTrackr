@extends('teacher/layout_teacher')
@section('title','Attendent List')
@section('subject_table')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<form action="attendent_list" method="get">
    <label for="subject">Subject</label>
    <select name="subject" id="subject" onchange="this.form.submit()">
        <option value="">Select Subject</option>
        @foreach($subject as $subj)
     
        <option value="{{$subj->id}}" {{request('subject')==$subj->id?'selected':''}}>{{$subj->subject->short_name.'/'.$subj->subject->student_class->program->name.'/'.$subj->subject->student_class->sem.'/'.$subj->subject->student_class->devision.'/'.$subj->subject->student_class->year}}</option>
                     
        @endforeach
    </select>
</form>
@if($student)
<a href="{{'generate_pdf/'.request('subject')}}"><button type="button" class="btn btn-info">PDF</button></a>
<a href="{{'generate_excel/'.request('subject')}}"><button type="button" class="btn btn-info">Excel</button></a>
<table class="table">
<tr>
    <th>Enrollment Number</th>
    <th>Name</th>
    <th>Total Class</th>
    <th>From</th>
    <th>To</th>
    <th>Present</th>
    <th>Pertentage</th>
    <th>Send Message</th>
</tr>   
@foreach($valid as $data)

<tr>
    
    <?php
    $sum=0;
    $present=0;
    $enter=0;
    $date=[];
    foreach ($student as $d) {
        if ($d->student->enrollment_number == $data && $d->staff_id == $id) {
            $date[]=$d->created_at;
            $sum++;
            $to=explode(" ",$d->created_at);
            $name=$d->student->name;
            $message=$d->student->student_id;
            if($d->attendance=='present'){
                $present++;
            }
        }

        
    }
    sort($date);
    $from=$date[0]; 
    $from=explode(" ",$from);
    $to = end($date);
    $to=explode(" ",$to);
    $pertentage=number_format($present/$sum*100,2);
    ?>
    <td>{{$data}}</td>
    <td>{{$name}}</td>
    <td>{{$sum}}</td>
    <td>{{$from[0]}}</td>
    <td>{{$to[0]}}</td>
   <td>{{$present}}</td>
   <td>{{$pertentage}}%</td>
   <td>
   <a href="{{'send-wa/'.$message.'/'.$pertentage.'/'.$subject_id}}"><button type="button" class="btn btn-info">Send Message</button></a>
   </td>
</tr>

@endforeach
</table>
@endif
@endsection