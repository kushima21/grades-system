@extends('layouts.default')

@vite(['resources/css/academic_grades.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@section('content')
<div class="academic-main-container">

    <div class="breadcrumb">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Academic Grades Archived</span>
    </div>

    <h2 class="my-header">Academic Grades Archived</h2>

    <div class="academic-folder-main-container">

        @foreach ($archivedData as $academicYear => $periods)
            @php $yearId = Str::slug($academicYear); @endphp

            {{-- ACADEMIC YEAR --}}
            <div class="folder" onclick="toggleFolder('year-{{ $yearId }}')">
                📁 Academic Year: {{ $academicYear }}
            </div>

            <div class="folder-content" id="year-{{ $yearId }}" style="display:none;">

                @foreach ($periods as $academicPeriod => $departments)
                    @php $periodId = Str::slug($academicPeriod); @endphp

                    {{-- ACADEMIC PERIOD --}}
                    <div class="folder period-folder"
                         onclick="toggleFolder('period-{{ $yearId }}-{{ $periodId }}')">
                        📂 Academic Period: {{ $academicPeriod }}
                    </div>

                    <div class="folder-content"
                         id="period-{{ $yearId }}-{{ $periodId }}"
                         style="display:none;">

                    @foreach ($departments as $department => $records)
                        @php $departmentId = Str::slug($department); @endphp

                        {{-- DEPARTMENT --}}
                        <div class="folder department-folder"
                            onclick="toggleFolder('dept-{{ $yearId }}-{{ $periodId }}-{{ $departmentId }}')">
                            📂 Department: {{ $department }}
                        </div>

                        {{-- STUDENT TABLE (hidden by default) --}}
                        <div class="student-table-container folder-content"
                            id="dept-{{ $yearId }}-{{ $periodId }}-{{ $departmentId }}"
                            style="display:none; margin-left:30px;">

                            <table>
                                <thead>
                                    <tr>
                                        <th>Department</th>
                                        <th>School ID</th>
                                        <th>Year Level</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Course No</th>
                                        <th>Descriptive Title</th>
                                        <th>Units</th>
                                        <th>Finals</th>
                                        <th>Final Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $groupedByStudent = $records->groupBy('studentID');
                                    @endphp

                                    @foreach ($groupedByStudent as $studentID => $grades)
                                        @php
                                            $student = $grades->first()->student;
                                            $firstRow = true;
                                        @endphp

                                        @foreach ($grades as $grade)
                                            <tr>
                                                {{-- Only show student info on the first course row --}}
                                                <td>{{ $firstRow ? ($student->department ?? $grade->department) : '' }}</td>
                                                
                                                <td>{{ $firstRow ? ($student->studentID ?? $grade->studentID) : '' }}</td>
                                                <td>{{ $firstRow ? ($student->year_level ?? 'N/A') : '' }}</td>
                                                <td>{{ $firstRow ? ($student->lname ?? $grade->name) : '' }}</td>
                                                <td>{{ $firstRow ? ($student->fname ?? '') : '' }}</td>
                                                <td>{{ $firstRow ? ($student->mname ?? '') : '' }}</td>

                                                {{-- Course info --}}
                                                <td>{{ $grade->course_no }}</td>
                                                <td>{{ $grade->descriptive_title }}</td>
                                                <td>{{ $grade->units }}</td>
                                                <td>{{ $grade->final }}</td>
                                                <td>{{ $grade->final_remark }}</td>
                                            </tr>
                                            @php $firstRow = false; @endphp
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                            <form method="POST" action="{{ route('registrar.academic_grades_archive.pdf') }}">
                                @csrf
                                <input type="hidden" name="academic_year">
                                <input type="hidden" name="academic_period">
                                <input type="hidden" name="department">
                                <input type="hidden" name="abbreviation">
                                <input type="hidden" name="studentID">
                                <input type="hidden" name="year_level">
                                <input type="hidden" name="lname">
                                <input type="hidden" name="fname">
                                <input type="hidden" name="mname">
                                <input type="hidden" name="gender">
                                <input type="hidden" name="nationality">
                                <input type="hidden" name="course_no">
                                <input type="hidden" name="descriptive_title">
                                <input type="hidden" name="units">
                                <input type="hidden" name="final">
                                <input type="hidden" name="final_remark">
                                <button type="submit" class="exportBtn">Generate PDF</button>
                            </form>
                        </div> <!-- end student table -->
                    @endforeach


                    </div> <!-- end period -->

                @endforeach
            </div> <!-- end year -->

        @endforeach

    </div>
</div>

<script>
function toggleFolder(id) {
    const content = document.getElementById(id);
    if (!content) return;
    content.style.display = content.style.display === 'block' ? 'none' : 'block';
}
</script>
@endsection
