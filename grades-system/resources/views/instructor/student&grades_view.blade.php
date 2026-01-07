@extends('layouts.default')
@vite(['resources/css/grading&score.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

@section('content')

<div class="grading-score-main-container">

    {{-- NAV PATH --}}
    <div class="span">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Student Grades</span>
    </div>

    {{-- PAGE HEADER --}}
    <h2 class="my-header">Student Grades</h2>

    {{-- SUBJECT DETAILS --}}
    <h2 class="student-grades-sub-header">
        <p class="student-grades-head">{{ $class->course_no }}</p>
        {{ $class->descriptive_title }}
    </h2>

    {{-- GLOBAL HEADER TOGGLE RAW --}}
    <div class="s-header-top">
        <button type="button" class="show-rawBtn" id="toggleRawBtn">Show Raw Columns</button>
    </div>

    @php
        // Group grades by department
        $grouped = $grades->groupBy('department');

        // Initialize flags for hiding Initialize button
        $anyLocked = false;
        $anySubmitted = false;

        // Check if ANY department is locked or submitted
        foreach($departmentStatus as $ds){
            if($ds->status == 'Locked') $anyLocked = true;
            if($ds->submit_status == 'Submitted') $anySubmitted = true;
        }
    @endphp



    {{-- DISPLAY GRADES PER DEPARTMENT --}}
    @foreach ($grouped as $department => $students)
    <div class="student-grades-m-container">

        {{-- DEPARTMENT HEADER --}}
        <h3 class="student-department-h">{{ $department }}</h3>

        {{-- STATUS DISPLAY --}}
        <div class="status-container">

            {{-- LOCK STATUS --}}
            <span class="status-span">
                Status:
                {{ $departmentStatus[$department]->status ?? 'Not Yet Locked' }}
            </span>

            {{-- SUBMIT STATUS --}}
            <span class="status-span">
                Submit to Dean Status:
                {{ $departmentStatus[$department]->submit_status ?? 'Not Submitted' }}
            </span>

            {{-- DEAN APPROVAL --}}
            <span class="status-span">
                Dean Approval:
                {{ $departmentStatus[$department]->dean_status ?? 'Pending' }}
            </span>

            {{-- REGISTRAR APPROVAL --}}
            <span class="status-span">
                Registrar Approval:
                {{ $departmentStatus[$department]->registrar_status ?? 'Pending' }}
            </span>

        </div>



        {{-- TABLE WRAPPER --}}
        <div class="student-grades-wrapper">

            {{-- ===================== GRADES TABLE ===================== --}}
            <table class="student-grades-table-container">

                {{-- ---------- TABLE HEADER ---------- --}}
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    @if($class->academic_period !== 'Summer')
                    <th>Prelim</th>
                    @endif
                        @if($class->academic_period !== 'Summer')
                        <th class="raw-col">Mid-Term Raw</th>
                    @endif
                        <th>Mid-Term</th>
                    @if($class->academic_period !== 'Summer')
                        <th class="raw-col">Semi-Final Raw</th>
                        <th>Semi Finals</th>
                    @endif

                    <th class="raw-col">Final Raw</th>
                    <th>Finals</th>
                    <th>Remarks</th>
                </tr>
                </thead>

                {{-- ---------- TABLE BODY ---------- --}}
                <tbody>
                @foreach($students as $g)
                    <tr>
                        <td>{{ $g->name }}</td>
                        <td>{{ $g->department }}</td>
                        <td>{{ $g->midterm_raw ?? '-' }}</td>

                        @if($class->academic_period !== 'Summer')
                            <td class="raw-col">{{ $g->midterm_raw ?? '-' }}</td>
                            <td>{{ $g->midterm ?? '-' }}</td>
                            <td class="raw-col">{{ $g->semi_finals_raw ?? '-' }}</td>
                            <td>{{ $g->semi_finals ?? '-' }}</td>
                        @endif

                        <td class="raw-col">{{ $g->final_raw ?? '-' }}</td>
                        <td>{{ $g->final ?? '-' }}</td>
                        <td>{{ $g->remarks ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            {{-- ===================== BUTTON LOGIC PER DEPARTMENT ===================== --}}
            @php
                $locked = isset($departmentStatus[$department]) 
                        && $departmentStatus[$department]->status === 'Locked';

                $submitted = isset($departmentStatus[$department]) 
                            && $departmentStatus[$department]->submit_status === 'Submitted';
            @endphp

            {{-- SHOW only when NOT locked & NOT submitted --}}
            @if(!($locked && $submitted))
                <div class="gradeBTn">

                    {{-- ---------- LOCK BUTTON ---------- --}}
                    <form method="POST" 
                        action="{{ $locked ? route('unlock.grades') : route('lock.grades') }}">
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

                        <button type="submit" class="lockBtn">
                            {{ $locked ? 'Unlock in '.$department : 'Lock in '.$department }}
                        </button>
                    </form>

                    {{-- ---------- SUBMIT TO DEAN (only if locked) ---------- --}}
                    @if($locked)
                        <form method="POST" action="{{ route('submit.to.dean') }}">
                            @csrf

                            <input type="hidden" name="classID" value="{{ $class->id }}">
                            <input type="hidden" name="department" value="{{ $department }}">

                            @foreach($students as $index => $g)
                                <input type="hidden" name="grades[{{ $index }}][studentID]" value="{{ $g->studentID }}">
                            @endforeach

                            <button type="submit" class="subToDeanBTn">
                                Submit to Dean in {{ $department }}
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

    </div>
    @endforeach



        {{-- INITIALIZE BUTTON - HIDE WHEN LOCKED OR SUBMITTED --}}
        {{-- INITIALIZE BUTTON --}}
        @if($gradesToInitializeExist)
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

        @else
        <p class="in-class waiting" style="padding-bottom: 20px;">Grades already initialized — waiting for progression.</p>
        @endif

</div>



{{-- RAW COLUMNS TOGGLE SCRIPT --}}
<style>
    .student-grades-table-container .raw-col { display: none; }
    .student-grades-table-container.show-raw .raw-col { display: table-cell; }
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
