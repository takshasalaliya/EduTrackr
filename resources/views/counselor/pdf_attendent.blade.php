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
            <td>{{$class}}</td>
        </tr>
        <tr>
            <th>Class Counselor:</th>
            <td>{{$teacher}}</td>
        </tr>
        <tr>
            <th>Subject:</th>
            <td>{{$subject}}</td>
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
                @foreach($valid as $data)
                <tr>
                    <?php
                        $sum = 0;
                        $present = 0;
                        $date = [];
                        foreach ($student as $d) {
                            if ($d->student->enrollment_number == $data && $d->staff_id == $id) {
                                $date[] = $d->created_at;
                                $sum++;
                                $name = $d->student->name;
                                
                                if ($d->attendance == 'present') {
                                    $present++;
                                }
                            }
                        }
                        
                        sort($date);
                        $from = $date[0];
                        $from=explode(" ",$from);
                        $to = end($date);
                        $to=explode(" ",$to);
                        $percentage = number_format(($present / $sum) * 100, 2);
                    ?>
                    <td class="text-center">{{$number++}}</td>
                    <td class="text-center">{{$data}}</td>
                    <td class="text-center">{{$name}}</td>
                    <td class="text-center">{{$from[0]}}</td>
                    <td class="text-center">{{$to[0]}}</td>
                    <td class="text-center">{{$sum}}</td>
                    <td class="text-center">{{$present}}</td>
                    <td class="text-center">{{$percentage}}%</td>
                </tr>
              
                @endforeach
            </tbody>
        </table>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
