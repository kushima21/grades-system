@extends('layouts.default')
@vite(['resources/css/grading&score.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

@section('content')

<div class="grading-score-main-container">
    <div class="span">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Student Grades</span>
    </div>

    <h2 class="my-header">Student Grades</h2>

    <h2 class="student-grades-sub-header">
        <p class="student-grades-head">{{ $class->course_no }}</p>
        {{ $class->descriptive_title }}
    </h2>

    {{-- GLOBAL HEADER --}}
    <div class="s-header-top">
        <button type="button" class="show-rawBtn" id="toggleRawBtn">Show Raw Columns</button>
    </div>

    @php
        $grouped = $grades->groupBy('department');
    @endphp

    {{-- STUDENTS BY DEPARTMENT --}}
    @foreach($grouped as $department => $students)
    <div class="student-grades-m-container">
        {{-- Department Title --}}
        <h3 class="student-department-h">{{ $department }}</h3>

        {{-- Status Section --}}
        <div class="status-container">
            <span class="status-span">Status: Not Locked Yet</span>
            <span class="status-span">Submit to Dean Status: Not Submitted Yet</span>
            <span class="status-span">Dean Approval: Not Approved Yet</span>
            <span class="status-span">Registrar Approval: Not Approved Yet</span>
        </div>

        <div class="student-grades-wrapper">
            <table class="student-grades-table-container">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Prelim</th>
                        <th class="raw-col">Mid-Term Raw</th>
                        <th>Mid-Term</th>
                        <th class="raw-col">Semi-Final Raw</th>
                        <th>Semi Finals</th>
                        <th class="raw-col">Final Raw</th>
                        <th>Finals</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $g)
                    <tr>
                        <td>{{ $g->name }}</td>
                        <td>{{ $g->department }}</td>
                        <td>{{ $g->prelim ?? '-' }}</td>
                        <td class="raw-col">{{ $g->midterm_raw ?? '-' }}</td>
                        <td>{{ $g->midterm ?? '-' }}</td>
                        <td class="raw-col">{{ $g->semi_finals_raw ?? '-' }}</td>
                        <td>{{ $g->semi_finals ?? '-' }}</td>
                        <td class="raw-col">{{ $g->final_raw ?? '-' }}</td>
                        <td>{{ $g->final ?? '-' }}</td>
                        <td>{{ $g->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- LOCK FORM PER DEPARTMENT --}}
            <div class="gradeBTn">
                <form method="POST" action="{{ route('lock.grades') }}">
                    @csrf
                    <input type="hidden" name="classID" value="{{ $class->id }}">
                    <input type="hidden" name="department" value="{{ $department }}">

                    @foreach($students as $index => $g)
                        <input type="hidden" name="grades[{{ $index }}][studentID]" value="{{ $g->studentID }}">
                        <input type="hidden" name="grades[{{ $index }}][prelim]" value="{{ $g->prelim }}">
                        <input type="hidden" name="grades[{{ $index }}][midterm]" value="{{ $g->midterm }}">
                        <input type="hidden" name="grades[{{ $index }}][semi_finals]" value="{{ $g->semi_finals }}">
                        <input type="hidden" name="grades[{{ $index }}][final]" value="{{ $g->final }}">
                        <input type="hidden" name="grades[{{ $index }}][remarks]" value="{{ $g->remarks }}">
                    @endforeach

                    <button type="submit" class="lockBtn">Lock in {{ $department }}</button>
                </form>
            </div>

        </div>
    </div>
    @endforeach

    {{-- INITIALIZE GRADES --}}
   <div class="initialBtn">
        <form method="POST" action="{{ route('initialize.grades') }}" id="initializeForm">
            @csrf
            @foreach($grades as $index => $g)
                <input type="hidden" name="grades[{{ $index }}][classID]" value="{{ $g->classID }}">
                <input type="hidden" name="grades[{{ $index }}][studentID]" value="{{ $g->studentID }}">
                <input type="hidden" name="grades[{{ $index }}][prelim]" value="{{ $g->prelim }}">
                <input type="hidden" name="grades[{{ $index }}][midterm]" value="{{ $g->midterm }}">
                <input type="hidden" name="grades[{{ $index }}][semi_finals]" value="{{ $g->semi_finals }}">
                <input type="hidden" name="grades[{{ $index }}][final]" value="{{ $g->final }}">
            @endforeach

            <button type="submit" class="initialyzeBtn">Initialize Grades</button>
        </form>
    </div>
     <p class="in-class">Initialize first before locking the grades!</p>

</div>

<style>
    .student-grades-table-container .raw-col {
        display: none;
    }
    .student-grades-table-container.show-raw .raw-col {
        display: table-cell;
    }
</style>

<script>
    const toggleButton = document.getElementById('toggleRawBtn');
    const allTables = document.querySelectorAll('.student-grades-table-container');

    toggleButton.addEventListener('click', () => {
        allTables.forEach(table => table.classList.toggle('show-raw'));
        toggleButton.textContent =
            allTables[0].classList.contains('show-raw')
                ? 'Hide Raw Columns'
                : 'Show Raw Columns';
    });
</script>
@endsection
