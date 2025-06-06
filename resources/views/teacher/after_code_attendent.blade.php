@extends('teacher/layout_teacher')
@section('title','Attendent List')
@section('after_code_attendent')
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
@if($students)
<table class="table">
    <tr>
        <th>Name</th>
        <th>Status</th>
    </tr>
    @foreach($students as $student)
    @if($student->attendance=='present')
    <tr>
        <td>{{ $student->student->name }}</td>
        <td>{{$student->attendance}}</td>
    </tr>
    @endif
    @endforeach
    @foreach($students as $student)
    @if($student->attendance=='absent')
    <tr>
        <td>{{ $student->student->name }}</td>
        <td>{{$student->attendance}}</td>
    </tr>
    @endif
    @endforeach
</table>

<h2>Make Attendent Of another Student</h2>
@if($duplicated)
@foreach($duplicated as $data)
    <h5>{{$data->in_class}} Make a Attendent Of {{$data->out_class}}</h5><br>

@endforeach
@endif
<h5>No one fill any one attendent</h5>
<a href="{{'/delete_code/'.$code}}"><button type="button" class="btn btn-info">Delete Code</button></a>
@endif
@if($students=='no')
<h1>no data found</h1>
@endif
@endsection