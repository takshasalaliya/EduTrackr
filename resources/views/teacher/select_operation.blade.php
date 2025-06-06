@extends('teacher/layout_teacher')
@section('title','Select')
@section('attendent_before')


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
            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Select Your Requirement</h3>
            
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
            
            <form action="selectes_data" method="get"> 
                @csrf

              <div class="row">
                
  
                <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                  <label class="form-label" for="subject" name="subject">Subject</label>
                    <select name="subject" id="subject" class="form-control form-control-lg">
                      <option value="" selected>Select Class</option>
                      
                      @foreach($subjects as $subject)
                    
                      <option value="{{$subject->id}}" {{request('subject')==$subject->id?'selected':''}}>{{$subject->subject->short_name.'/'.$subject->subject->student_class->program->name.'/'.$subject->subject->student_class->sem.'/'.$subject->subject->student_class->devision.'/'.$subject->subject->student_class->year}}</option>
                     
                      @endforeach
                    </select>
                    <span class="alert">@error('subject'){{"⚠️".$message}}@enderror</span>
                  </div>

                  <div class="col-md-6 mb-4 pb-2">

<div data-mdb-input-init class="form-outline">
<label class="form-label" for="unit" name="unit">Unit</label>
  <select name="unit" id="Unit" class="form-control form-control-lg">
    <option value="1">Unit 1</option>
    <option value="2">Unit 2</option>
    <option value="3">Unit 3</option>
    <option value="4">Unit 4</option>
    <option value="5">Unit 5</option>
    <option value="6">Unit 6</option>
  </select>
  <span class="alert">@error('unit'){{"⚠️".$message}}@enderror</span>
</div>

</div>
                </div>
            
             
              <div class="row">
              <div class="col-md-6 mb-4 pb-2">

                  <div data-mdb-input-init class="form-outline">
                  <label class="form-label" for="leacture" name="leacture">Leacture</label>
                    <select name="leacture" id="leacture" class="form-control form-control-lg">
                      <option value="1">Leacture 1</option>
                      <option value="2">Leacture 2</option>
                      <option value="3">Leacture 3</option>
                      <option value="4">Leacture 4</option>
                      <option value="5">Leacture 5</option>
                      <option value="6">Leacture 6</option>
                    </select>
                    <span class="alert">@error('leacture'){{"⚠️".$message}}@enderror</span>
                  </div>

                </div>
                <div class="col-md-6 mb-4 pb-2" >
                    
                    <div data-mdb-input-init class="form-outline">
                    <label class="form-label" for="date" name="date">Date</label>
                    <input type="date" name="date" id="date">
                      <span class="alert">@error('date'){{"⚠️".$message}}@enderror</span>
                    </div>
                    
                    </div>
 
                    </div>
              <div class="mt-4 pt-2">
                <input data-mdb-ripple-init class="btn btn-primary btn-lg" name="submit" type="submit" value="submit" />
                <input data-mdb-ripple-init class="btn btn-primary btn-lg" name="submit" type="submit" value="edit" />
                <input data-mdb-ripple-init class="btn btn-primary btn-lg" name="submit" type="submit" value="generate" />
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