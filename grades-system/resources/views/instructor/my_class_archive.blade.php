@extends('layouts.default')
@vite(['resources/css/my_class_archive.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@section('content')
<div class="my-class-archived-main-container">

    <div class="breadcrumb">
        <span>Admin</span> <span>></span> <span>Manage</span> <span>></span> <span>My Class Archived</span>
    </div>

    {{-- Grade Archive View --}}
    <div class="grade-archive-view" id="gradeArchiveView">
        <div class="a-header-close">
            <h2 class="archive-view-header">CS 10</h2>
            <span class="close-icon" id="closeGradeView">✖</span>
        </div>
        <h3 class="archive-view-subheader">Programming 1</h3>

        <div class="acad-details">
            <span class="instructor-label">Instructor:</span>
            <span class="instructor-value"></span>
            <span class="schedule-label">Schedule:</span>
            <span class="schedule-value"></span>
        </div>

        <div class="acad-container">
            <span class="acad-year-label">Academic Year:</span>
            <span class="acad-year-value"></span>
            <span class="acad-period-label">Academic Period:</span>
            <span class="acad-period-value"></span>
        </div>

        <div class="grades-wrapper-container">
            <h3 class="grades-header">Final Grades</h3>
            <table class="grades-table-container">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Prelim</th>
                        <th>Midterm</th>
                        <th>Semi-Finals</th>
                        <th>Final</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="grades-table-body"></tbody>
            </table>
        </div>
    </div>

    {{-- Archive Folder --}}
    <h2 class="my-header">My Class Archived</h2>
    <div class="class-archive-container">
  @foreach ($archivedData as $academic_year => $periodGroups)
    <div class="folder" onclick="toggleFolder('year-{{ $academic_year }}')">
        📁 Academic Year: {{ $academic_year }}
    </div>
    <div class="folder-content" id="year-{{ $academic_year }}">
        @foreach ($periodGroups as $academic_period => $classGroups)
            <div class="folder period-folder" onclick="toggleFolder('period-{{ $academic_year }}-{{ $academic_period }}')">
                📂 Academic Period: {{ $academic_period }}
            </div>
            <div class="folder-content" id="period-{{ $academic_year }}-{{ $academic_period }}">
                @foreach ($classGroups as $classID => $instructorGroups)
              @php
                    // Get the first student record in finalGrades for this classID
                    $scheduleKeyPrefix = $academic_year . '|' . $academic_period . '|' . $classID;
                    $scheduleRecord = collect($finalGrades)
                        ->filter(fn($grades, $key) => str_starts_with($key, $scheduleKeyPrefix))
                        ->first();

                    if ($scheduleRecord && count($scheduleRecord) > 0) {
                        $firstGrade = $scheduleRecord[0];
                        $course_no = $firstGrade->course_no ?? 'N/A';
                        $program = $firstGrade->program ?? 'N/A';
                    } else {
                        $course_no = 'N/A';
                        $program = 'N/A';
                    }
                @endphp
                    <div class="folder subject-folder" onclick="toggleFolder('class-{{ $classID }}')">
                       📂 Course No: {{ $course_no }} / {{ $program }}
                    </div>
                    <div class="folder-content" id="class-{{ $classID }}">
                        @foreach ($instructorGroups as $instructor => $titleGroups)
                            <h4 class="i-header">Instructor: {{ $instructor }}</h4>
                            @foreach ($titleGroups as $descriptive_title => $termGroups)
                                <h5 class="d-header">Descriptive Title: {{ $descriptive_title }}</h5>
                                @php
                                    // Include classID in the scheduleKey for alignment
                                    $scheduleKey = $academic_year . '|' . $academic_period . '|' . $classID . '|' . $instructor . '|' . $descriptive_title;
                                    $scheduleRecord = collect($finalGrades)
                                        ->filter(fn($records, $key) => str_starts_with($key, $scheduleKey))
                                        ->first();
                                    $schedule = $scheduleRecord ? $scheduleRecord[0]->schedule : 'N/A';
                                @endphp
                                <h5 class="s-header">Schedule: {{ $schedule }}</h5>

                                <div class="table-container">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Prelim</th>
                                                <th>Midterm</th>
                                                <th>Semi-Finals</th>
                                                <th>Final</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($finalGrades as $key => $grades)
                                                @if (str_starts_with($key, $scheduleKey))
                                                    @php $finalGrade = $grades[0]; @endphp
                                                    <tr>
                                                        <td>{{ $finalGrade->studentID }}</td>
                                                        <td>{{ $finalGrade->name }}</td>
                                                        <td>{{ $finalGrade->prelim }}</td>
                                                        <td>{{ $finalGrade->midterm }}</td>
                                                        <td>{{ $finalGrade->semi_finals }}</td>
                                                        <td>{{ $finalGrade->final }}</td>
                                                        <td>{{ $finalGrade->remarks }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <form method="POST" action="{{ route('instructor.generate_gradesheet_pdf') }}" target="_blank" class="generateGrade">
                                        @csrf
                                        <input type="hidden" name="academic_year" value="{{ $academic_year }}">
                                        <input type="hidden" name="academic_period" value="{{ $academic_period }}">
                                        <input type="hidden" name="subject_code" value="{{ $course_no }}">
                                        <input type="hidden" name="course_no" value="{{ $course_no }}">
                                        <input type="hidden" name="classID" value="{{ $classID }}">
                                        <input type="hidden" name="program" value="{{ $program }}"> {{-- ✅ send program --}}
                                        <input type="hidden" name="instructor" value="{{ $instructor }}">
                                        <input type="hidden" name="descriptive_title" value="{{ $descriptive_title }}">
                                        <button type="submit" class="gBtn"><i class="fa-solid fa-file-pdf"></i> Generate PDF</button>
                                    </form>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endforeach



    </div>

</div>

<script>
function toggleFolder(id) {
    const content = document.getElementById(id);
    content.style.display = content.style.display === 'block' ? 'none' : 'block';
}

// Close grade view
const gradeArchiveView = document.getElementById('gradeArchiveView');
const closeGradeView = document.getElementById('closeGradeView');
if (closeGradeView) {
    closeGradeView.addEventListener('click', () => gradeArchiveView.style.display = 'none');
}
</script>
@endsection
