@extends('teacher/layout_teacher')
@section('title','Edit Attendent')
@section('edit_attendent')


<head>
    <style>.gradient-custom {
/* fallback for old browsers */
background: #ffffff;

/* Chrome 10-25, Safari 5.1-6 */
background: #ffffff;

/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
background: #ffffff;
}

.card-registration .select-input.form-control[readonly]:not([disabled]) {
font-size: 1rem;
line-height: 2.15;
padding-left: .75em;
padding-right: .75em;
}
.card-registration .select-arrow {
top: 13px;
}</style>
</head>
<body>


<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Fill Attendent</h3>
            @if(session('success'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
              <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                <use xlink:href="#check-circle-fill"/>
              </svg>
              <div>
                {{ session('success') }}
              </div>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-success d-flex align-items-center" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
  <div>
    {{session('error')}}
  </div>

</div>
@endif       @foreach($datas as $data)
            @if($data->id==$pervious->subject)
            @php
                      $year=explode('/',$data->subject->student_class->year);
                      $year1=explode('-',$year[0]);
                      $year2=explode('-',$year[1]);
                      @endphp
            <h6>Class: {{$data->subject->student_class->program->name.'/'.$year1[0].'-'.$year2[0].'/'.$data->subject->student_class->sem}} &nbsp; Devision: {{$data->subject->student_class->devision}} &nbsp;
               <br> Subject: {{$data->subject->subject_name}} &nbsp;<br>Teacher: {{$data->teacher->name}}  <br>
                Leacture Number: {{$pervious->leacture}} &nbsp;Date: {{$pervious->date}} </h6>
            @endif
            @endforeach
            <br><br>
            <form action="edit_attendendent" method="post"> 
                @csrf
                
                <div class="row">

               
                <div class="col-md-6 mb-4 pb-2">
                    @if($attendent)
                @foreach($attendent as $data)
                <b style="font-size:20px; ">{{++$sum}} &nbsp;<label for="{{$sum}}">{{$data->student->name}}</label></b>
                  <input type="text" value="{{$data->student->student_id}}" name="student[]" hidden>
                 <label for="{{$sum.'p'}}" style="margin-left:20px;">P</label>
                 <input type="radio" name="{{$data->student->student_id}}" value="present" id="{{$sum.'p'}}" {{$data->attendance=='present'?'checked':''}}>
                <label for="{{$sum.'a'}}">A</label>
                <input type="radio" name="{{$data->student->student_id}}" value="absent" id="{{$sum.'a'}}" {{$data->attendance=='absent'?'checked':''}}><br>
                @endforeach
                @endif
                @if($sum==0)
                <h2>you will not take attendent on this day</h2>
                @endif

                  <div data-mdb-input-init class="form-outline">
                  <input type="text" value="{{$pervious->subject}}" name="staff_id" hidden>
                  <input type="text" value="{{$pervious->leacture}}" name="leacture" hidden>
                  <input type="date" value="{{$pervious->date}}" name="date" hidden>
                



                    <span class="alert">@error('subject'){{"⚠️".$message}}@enderror</span>
                  </div>

                </div>
              </div>

             
                    
              <div class="mt-4 pt-2">
                <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit" />
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
@endsection