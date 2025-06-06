@extends('teacher/layout_teacher')
@section('title','Teacher Dashboard')
@section('dashboard')

               <!-- Main Content -->
            <main class="col-md ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Welcome, Teacher</h1>
                </div>

                <div class="row">
                    <!-- Card 1 -->
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Manage Attendance</h5>
                                <p class="card-text">Track and manage attendance records for your students.</p>
                                <a href="/select" class="btn btn-primary">Go to Attendance</a>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Card 2 -->
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Generate Reports</h5>
                                <p class="card-text">Create detailed reports for analysis and review.</p>
                                <a href="/attendent_list" class="btn btn-primary">View Reports</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <center><h1>Total Lecture Of Each Unit</h1></center>
   
                     <table class="table">
                        <tr>
                            <th>Class</th>
                            <th>Unit No. = Total Lecture</th>
                        </tr>
                        @foreach($subjects as $subject)
                        <tr>
                            <td>
                     {{$subject->subject->short_name.'/'.$subject->subject->student_class->program->name.'/'.$subject->subject->student_class->sem.'/'.$subject->subject->student_class->devision.'/'.$subject->subject->student_class->year}}
                            </td>
                                                       <td>
                           @php
    // Initialize unit counters
    $unit_total = 0;
    $a1 = $a2 = $a3 = $a4 = $a5 = $a6 = 0;
    $id=[];
    foreach ($units as $unit) {
        $id=$unit;
        break;
    }
     foreach ($units as $unit){
     if($id->student_id==$unit->student_id && $subject->id == $unit->staff_id){
                
            if ($unit->unit == 1) $a1++;
            if ($unit->unit == 2) $a2++;
            if ($unit->unit == 3) $a3++;
            if ($unit->unit == 4) $a4++;
            if ($unit->unit == 5) $a5++;
            if ($unit->unit == 6) $a6++;
            
            
        }
     }

    // Prepare output
    $unitOutput = [];
    if ($a1 > 0) $unitOutput[] = "unit 1 = " . $a1;
    if ($a2 > 0) $unitOutput[] = "unit 2 = " . $a2;
    if ($a3 > 0) $unitOutput[] = "unit 3 = " . $a3;
    if ($a4 > 0) $unitOutput[] = "unit 4 = " . $a4;
    if ($a5 > 0) $unitOutput[] = "unit 5 = " . $a5;
    if ($a6 > 0) $unitOutput[] = "unit 6 = " . $a6;
@endphp

@if (!empty($unitOutput))
    {!! implode('<br>', $unitOutput) !!}
@else
    No unit data available.
@endif

                        </td>
                        </tr>
                        @endforeach    
                     </table>
        </div>
    </div>

    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection