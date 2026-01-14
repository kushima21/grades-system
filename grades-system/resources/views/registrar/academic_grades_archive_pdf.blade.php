<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<style>
    body {
        width: 100%;
    }
    .student-main-container {
     width: 100%;
     height: 100%;
    }
    
.table-wrapper {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}


th {
    font-size: 8px;
    border: 1px solid #000;
    padding: 500px;
}

td {
    font-size: 7px;
    border: 1px solid #2e2d2d;
    padding: 10px;
    padding: 20px 0;
}
</style>
    <div class="student-main-container">
        @foreach ($archivedData as $academicYear => $periods)
    @foreach ($periods as $academicPeriod => $departments)
        @foreach ($departments as $department => $records)
            <table class="table-wrapper">
             
                <thead>
                    <tr>
                        <th style="width: 150px;">Department</th>
                        <th style="width: 50px;">Major</th>
                        <th style="width: 80px;">Curriculum</th>
                        <th style="width: 80px;">School ID</th>
                        <th style="width: 50px;">Year Level</th>
                        <th style="width: 70px;">Last Name</th>
                        <th style="width: 80px;">First Name</th>
                        <th style="width: 70px;">Middle Name</th>
                        <th style="width: 50px;">Gender</th>
                        <th style="width: 70px;">Nationality</th>
                        <th style="width: 50px;">Course No</th>
                        <th style="width: 150px;">Descriptive Title</th>
                        <th style="width: 50px;">Units</th>
                        <th style="width: 50px;">Final</th>
                        <th style="width: 40px;">Remark</th>
                    </tr>
                </thead>

                <tbody>
                @php
                    $grouped = $records->groupBy('studentID');
                @endphp

                @foreach ($grouped as $studentID => $grades)
                    @php
                        $student = $grades->first()->student;
                        $firstRow = true;
                    @endphp

                    @foreach ($grades as $grade)
                        <tr>
                            <td style="width: 150px;">{{ $firstRow ? $department : '' }}</td>
                            <td style="width: 50px;"></td>
                            <td style="width: 80px;">{{ $firstRow ? ($student->abbreviation ?? '') : '' }}</td>
                            <td style="width: 80px;">{{ $firstRow ? $studentID : '' }}</td>
                            <td style="width: 50px;">{{ $firstRow ? ($student->year_level ?? '') : '' }}</td>
                            <td style="width: 70px;">{{ $firstRow ? ($student->lname ?? '') : '' }}</td>
                            <td style="width: 80px;">{{ $firstRow ? ($student->fname ?? '') : '' }}</td>
                            <td style="width: 70px;">{{ $firstRow ? ($student->mname ?? '') : '' }}</td>
                            <td style="width: 50px;">{{ $firstRow ? ($student->gender ?? '') : '' }}</td>
                            <td style="width: 70px;">{{ $firstRow ? ($student->nationality ?? '') : '' }}</td>

                            <td style="width: 50px;">{{ $grade->course_no }}</td>
                            <td style="width: 150px;">{{ $grade->descriptive_title }}</td>
                            <td style="width: 50px; text-align: center;" >{{ $grade->units }}</td>
                            <td style="width: 50px; text-align: center;">{{ $grade->final }}</td>
                            <td style="width: 40px;">{{ $grade->final_remark }}</td>
                        </tr>
                        @php $firstRow = false; @endphp
                    @endforeach
                @endforeach
                </tbody>
            </table>
        @endforeach
    @endforeach
@endforeach
    </div>
    
</body>
</html>