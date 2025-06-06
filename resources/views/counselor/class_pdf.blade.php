<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    @page {
        size: A4;
        margin: 20mm;
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 5px;
        text-align: center;
        border: 1px solid #ccc;
    }
    .page-break {
        page-break-before: always;
    }
</style>

</head>
<body>
    @php
$number=1;
$image=public_path('image/3-removebg-preview.png');
    @endphp
   
<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents($image))}}" alt="semcom" style="width:100%;">

    <table class="table table-bordered">
        <tr>
            <th>Class & Semester:</th>
            <td>{{$student_class}}</td>
        </tr>
        <tr>
            <th>Class Counselor:</th>
            <td>{{Auth::user()->name}}</td>
        </tr>
            <th>Class Strength:</th>
            <td>{{$count}}</td>
        </tr>
    </table>
        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>Sr. No.</th>
                    <th>Enrollment Number</th>
                    <th>Name</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Total Classes</th>
                    <th>Present</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datas as $data)
                <tr>
                <?php
                 $data=explode('&',$data);
                ?>
                    <td class="text-center">{{$number++}}</td>
                    <td class="text-center">{{$data[0]}}</td>
                    <td class="text-center">{{$data[1]}}</td>
                    <td class="text-center">{{$data[2]}}</td>
                    <td class="text-center">{{$data[3]}}</td>
                    <td class="text-center">{{$data[4]}}</td>
                    <td class="text-center">{{$data[5]}}</td>
                    <td class="text-center">{{$data[6]}}%</td>
                </tr>
              
                @endforeach
            </tbody>
        </table>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
