<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            text-align: center;
        }

        .container {
            margin-top: 120px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 30px;
        }

        h2 {
            font-size: 18px;
            margin: 15px 0;
        }

        h3 {
            font-size: 16px;
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="container">

    @foreach ($archivedData as $academicYear => $periods)
        <h2>Academic Year: {{ $academicYear }}</h2>

        @foreach ($periods as $academicPeriod => $departments)
            <h3>Academic Period: {{ $academicPeriod }}</h3>

            @foreach ($departments as $department => $records)
                <h3>Department: {{ $department }}</h3>
                @break
            @endforeach
            @break
        @endforeach
        @break
    @endforeach
</div>

</body>
</html>
