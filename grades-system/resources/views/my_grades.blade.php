@extends('layouts.default')

@section('content')
    <div class="dashboard">
        <div class="header-container">
            <h1>My Grades</h1>
            {{-- <button id="toggleViewBtn"><i class="fa-solid fa-chart-bar"></i> View Grades in Graph</button> --}}
            <canvas id="gradesChart" style="max-width: 600px; display: none; overflow: auto;"></canvas>


        </div>



        <!-- Filters -->
        <form method="GET" action="{{ route('my_grades') }}" class="filter-form">
            <select name="academic_year">
                <option value="">Select Academic Year</option>
                @foreach ($academicYears as $year)
                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                        {{ $year }}</option>
                @endforeach
            </select>

            <select name="academic_period">
                <option value="">Select Academic Period</option>
                @foreach ($academicPeriods as $period)
                    <option value="{{ $period }}" {{ request('academic_period') == $period ? 'selected' : '' }}>
                        {{ $period }}</option>
                @endforeach
            </select>

            <button type="submit">Filter</button>
        </form>

        <style>
            .header-container {
                display: flex;
                flex-direction: column;
                width: 100%;
                margin-bottom: 10px;
            }

            #toggleViewBtn {
                padding: 5px;
                cursor: pointer;
                margin-top: 10px;
                border-radius: 5px;
            }

            .filter-form {
                display: flex;
                gap: 10px;
                margin-bottom: 15px;
            }

            .filter-form select {
                padding: 5px;
            }

            .filter-form button {
                background: var(--ckcm-color4);
                color: white;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
            }
        </style>
        <div id="gradesTable" class="grades-container">
            @if ($grades->isEmpty())
                <p style="color: var(--color1)">No grades available.</p>
            @else
                @foreach ($grades as $academic_year => $periodGroups)
                    <h2 style="color: var(--ckcm-color4); margin-bottom: 10px;">Academic Year: {{ $academic_year }}</h2>
                    @foreach ($periodGroups as $academic_period => $gradeList)
                        <h3 style="color: var(--color5); margin: 5px 0;">Academic Period: {{ $academic_period }}</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Descriptive Title</th>
                                    <th>Instructor</th>
                                    {{-- <th>Prelim</th>
                                    <th>Midterm</th>
                                    <th>Semi Finals</th> --}}
                                    <th>Finals</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradeList as $grade)
                                    <tr>
                                        <td>{{ $grade->subject_code }}</td>
                                        <td style="max-width:50px; overflow: auto;">{{ $grade->descriptive_title }}</td>
                                        <td>{{ $grade->instructor }}</td>
                                        {{-- <td
                                            style="color: {{ $grade->prelim <= 3.0 ? 'green' : 'red' }}; background-color:var(--color9b);">
                                            {{ $grade->prelim }}
                                        </td>
                                        <td
                                            style="color: {{ $grade->midterm <= 3.0 ? 'green' : 'red' }}; background-color:var(--color9b);">
                                            {{ $grade->midterm }}
                                        </td>
                                        <td
                                            style="color: {{ $grade->semi_finals <= 3.0 ? 'green' : 'red' }}; background-color:var(--color9b);">
                                            {{ $grade->semi_finals }}
                                        </td> --}}
                                        <td
                                            style="color: {{ $grade->final <= 3.0 ? 'green' : 'red' }}; background-color:var(--color9b);">
                                            {{ $grade->final }}
                                        </td>
                                        <td
                                            style="color: {{ strtolower($grade->remarks) == 'failed' ? 'red' : 'green' }}; background-color:var(--color9b);">
                                            {{ $grade->remarks }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstance = null;
        document.getElementById('toggleViewBtn').addEventListener('click', function() {
            const tableView = document.getElementById('gradesTable');
            const chartCanvas = document.getElementById('gradesChart');
            const btn = document.getElementById('toggleViewBtn');

            if (tableView.style.display === "none") {
                tableView.style.display = "block";
                chartCanvas.style.display = "none";
                btn.innerHTML = '<i class="fas fa-chart-bar"></i> View Grades in Graph'; // Graph icon
            } else {
                tableView.style.display = "none";
                chartCanvas.style.display = "block";
                btn.innerHTML = '<i class="fas fa-table"></i> View Grades in Table'; // Table icon

                if (!chartInstance) {
                    const ctx = chartCanvas.getContext('2d');
                    const subjects = @json($grades->pluck('subject_code'));
                    const prelimGrades = @json($grades->pluck('prelim'));
                    const midtermGrades = @json($grades->pluck('midterm'));
                    const semiFinalGrades = @json($grades->pluck('semi_finals'));
                    const finalGrades = @json($grades->pluck('final'));

                    const colors = ['#007f5f', '#2b9348', '#55a630', '#80b918'];
                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: subjects,
                            datasets: [{
                                    label: 'Prelim',
                                    data: prelimGrades.map(g => 5 - g),
                                    backgroundColor: colors[0]
                                },
                                {
                                    label: 'Midterm',
                                    data: midtermGrades.map(g => 5 - g),
                                    backgroundColor: colors[1]
                                },
                                {
                                    label: 'Semi Finals',
                                    data: semiFinalGrades.map(g => 5 - g),
                                    backgroundColor: colors[2]
                                },
                                {
                                    label: 'Finals',
                                    data: finalGrades.map(g => 5 - g),
                                    backgroundColor: colors[3]
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    min: 0, // Ensures bars grow from bottom
                                    max: 4, // Max height (since 5-1 = 4)
                                    ticks: {
                                        stepSize: 0.25,
                                        callback: function(value) {
                                            return 5 - value; // Displays grades correctly

                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection






<style>
    .dashboard {
        padding: 10px;
        overflow: auto;
    }



    .dashboard h1 {
        color: var(--ckcm-color4);

    }


    @media (max-width: 480px) {

        .header-container h1 {
            font-size: 1.6rem;
        }

        table th:nth-child(2),
        table td:nth-child(2),
        table th:nth-child(3),
        table td:nth-child(3) {
            display: none;
        }

        .grades-container table td,
        .grades-container table th {
            font-size: 1rem;
            padding: 5px;
        }
    }

    @media (max-width: 768px) {

        .grades-container table td,
        .grades-container table th {

            padding: 5px;
        }
    }
</style>


<!-- Full-screen Loader -->
<div id="loadingScreen"
    style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--ckcm-color1);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;">
    <div class="loader"></div>
</div>

<!-- CSS Loader Animation -->
<style>
    .loader {
        background: var(--ckcm-color1);
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>


<!-- JavaScript to Hide Loader -->
<script>
    window.onload = function() {
        setTimeout(function() {
            document.getElementById('loadingScreen').style.display = 'none';
        }, 1000);
    };
</script>