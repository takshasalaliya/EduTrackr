@extends('admin/layout')

@section('title','Subject and Teaching  Staff')
@section('editteachingstaff')

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
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Edit Subject and Teacher</h3>
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
@endif

            
            <form action="{{'/edit_teaching_staff/'.$datas->id}}" method="post"> 
                @csrf

              <div class="row">
                
 
                <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                  <label class="form-label" for="teacher" name="class">Teacher</label>
                    <select name="teacher" id="teacher" class="form-control form-control-lg">
                      <option value="default" selected>Select Class</option>
                      @foreach($teachers as $teacher)
                      <option value="{{$teacher->id}}" {{$teacher->id==$datas->staff_id?'selected':''}}>{{$teacher->name.'/'.$teacher->short_name}}</option>
                      @endforeach
                    </select>
                    <span class="alert">@error('teacher'){{"⚠️".$message}}@enderror</span>
                  </div>

                </div>
              </div>

              <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                  <label class="form-label" for="subject" name="subject">Subject</label>
                    <select name="subject" id="subject" class="form-control form-control-lg">
                      <option value="default" selected>Select Class</option>
                      @foreach($subjects as $subject)
                      <option value="{{$subject->subject_id}}" {{$subject->subject_id==$datas->subject_id?'selected':''}}>{{$subject->subject_name.'/'.$subject->short_name.'/'.$subject->subject_code}}</option>
                      @endforeach
                    </select>
                    <span class="alert">@error('subject'){{"⚠️".$message}}@enderror</span>
                  </div>

                </div>

                 <div class="col-md-6 mb-4">

                  <div data-mdb-input-init class="form-outline">
                  <label class="form-label" for="category">Category</label><br>
                   Required <input type="radio" id="category" name="category" value="required" />
                   Optional <input type="radio" id="category" name="category" value="optional"/>
                    <span class="alert">@error('category'){{"⚠️".$message}}@enderror</span>
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