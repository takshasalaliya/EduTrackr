@extends('teacher/layout_teacher')
@section('title','Attendent')
@section('attendent')
<head>
    <style>
        .gradient-custom {
            background: linear-gradient(to bottom right, #ffffff, #f1f1f1);
        }

        .card-registration {
            border-radius: 15px;
        }

        .alert svg {
            margin-right: 10px;
        }

        label {
            font-weight: 500;
        }

        input[type="radio"] {
            margin-left: 10px;
        }

        .student-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .student-name {
            flex-grow: 1;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-12 col-lg-9 col-xl-7">
                    <div class="card shadow-2-strong card-registration">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5 text-center">Fill Attendance</h3>

                            @if(session('success'))
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                                        <use xlink:href="#check-circle-fill" />
                                    </svg>
                                    <div>{{ session('success') }}</div>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Error:">
                                        <use xlink:href="#exclamation-triangle-fill" />
                                    </svg>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            @foreach($datas as $data)
                                @if($data->id == $pervious->subject)
                                    <h6>
                                        Class: {{ $data->subject->student_class->program->name }}/{{ $data->subject->student_class->year }}/{{ $data->subject->student_class->sem }}<br>
                                        Division: {{ $data->subject->student_class->devision }}<br>
                                        Subject: {{ $data->subject->subject_name }}<br>
                                        Teacher: {{ $data->teacher->name }}<br>
                                        Lecture Number: {{ $pervious->leacture }}<br>
                                        Unit: {{ $pervious->unit}}<br>
                                        Date: {{ $pervious->date }}
                                    </h6>
                                @endif
                            @endforeach

                            <form action="final_attendent" method="post">
                                @csrf
                                <input type="hidden" name="staff_id" value="{{ $pervious->subject }}">
                                <input type="hidden" name="leacture" value="{{ $pervious->leacture }}">
                                <input type="hidden" name="date" value="{{ $pervious->date }}">
                                <input type="hidden" name="unit" value="{{ $pervious->unit }}">
                                @if($attendent == 'yes')
                                    @if($yes)
                                        @foreach($datas as $data)
                                            @if($data->id == $pervious->subject)
                                                @foreach($students as $student)
                                                    @if($data->subject->class_id == $student->class_id)
                                                        <div class="student-row">
                                                            <span class="student-name">{{ ++$sum }}. {{ $student->name }}</span>
                                                            <input type="hidden" name="student[]" value="{{ $student->student_id }}">
                                                            <label for="{{ $sum.'p' }}">P</label>
                                                            <input type="radio" name="{{ $student->student_id }}" value="present" id="{{ $sum.'p' }}"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <label for="{{ $sum.'a' }}">A</label>
                                                            <input type="radio" name="{{ $student->student_id }}" value="absent" id="{{ $sum.'a' }}">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($optional)
                                        @foreach($optional as $data)
                                            @if($data)
                                                <div class="student-row">
                                                    <span class="student-name">{{ ++$sum }}. {{ $data->student->name }}</span>
                                                    <input type="hidden" name="student[]" value="{{ $data->student->student_id }}">
                                                    <label for="{{ $data->student->student_id.'p' }}">P</label>
                                                    <input type="radio" name="{{ $data->student->student_id }}" value="present" id="{{ $data->student->student_id.'p' }}">
                                                    <label for="{{ $data->student->student_id.'a' }}">A</label>
                                                    <input type="radio" name="{{ $data->student->student_id }}" value="absent" id="{{ $data->student->student_id.'a' }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($sum == 0)
                                        <h2 class="text-center">No students added for this subject.</h2>
                                    @endif
                                @elseif($attendent == 'no')
                                    <h1 class="text-center text-danger">Attendance already taken for this subject.</h1>
                                @endif

                                <span class="text-danger">@error('subject')⚠️ {{ $message }} @enderror</span>

                                <div class="mt-4 pt-2 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
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