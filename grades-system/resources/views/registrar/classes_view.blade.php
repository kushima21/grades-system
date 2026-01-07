@extends('layouts.default')
@vite(['resources/css/classes_view.css', 'resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="classes-view-main-container">

    {{-- Breadcrumb --}}
    <div class="span">
        <span>Admin</span> <span>></span>
        <span>Manage</span> <span>></span>
        <span>Classes</span> <span>></span>
        <span>Classes Details</span>
    </div>

    {{-- Add Student Modal --}}
    <div class="add-student-modal-container" id="addStudentModal">
        <h3 class="add-header">Add Student</h3>

        {{-- CSV Upload --}}
        <div class="csv-container">
            <form action="{{ route('class.importcsv', $classes->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="students_csv" id="students_csv" accept=".csv" required>
                <button type="submit" class="save-btn">
                    <i class="fa-solid fa-file-arrow-up"></i> Add Multiple Students
                </button>
            </form>
            <p class="csv-p">or add student individually</p>
        </div>

        {{-- Manual Add Form --}}
        <div class="class-view-form-container">
            <form method="POST" action="{{ route('class.addstudent', $classes->id) }}">
                @csrf
                <input type="hidden" name="program" value="{{ $classes->program }}">

                <div class="info-add">
                    <label for="studentSearch">Find Student</label>
                    <div id="studentDropdown" class="dropdown-menu"></div>
                    <input type="text" id="studentSearch" class="form-control" oninput="filterStudents()" placeholder="Search for a student...">
                </div>

                <div class="info-add"><label for="name">Name</label><input type="text" id="name" name="name" class="form-control" placeholder="Student Name" required readonly></div>
                <div class="info-add"><label for="student_id">Student ID</label><input type="text" id="student_id" name="student_id" class="form-control" placeholder="Student ID" required readonly></div>
                <div class="info-add"><label for="email">Email</label><input type="email" id="email" name="email" class="form-control" placeholder="Student Email" required readonly></div>
                <div class="info-add"><label for="gender">Gender</label><input type="text" name="gender" id="gender" class="form-control" placeholder="Student Gender" required readonly></div>
                <div class="info-add"><label for="department">Department</label><input type="text" name="department" id="department" class="form-control" placeholder="Student Department" required readonly></div>

                <div class="add-studentBtn">
                    <button type="submit" name="submit"><i class="fa-solid fa-file-arrow-up"></i> Add Student</button>
                    <button type="button" onclick="closeAddStudentModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Class Info --}}
    <h2 class="my-header">{{ $classes->course_no }}</h2>
    <span class="class-header">{{ $classes->descriptive_title }}</span>

    <div class="cv-subheader">
        <p class="cv-h">Academic Year: {{ $classes->academic_year }}</p>
        <p class="cv-h">Academic Period: {{ $classes->academic_period }}</p>
    </div>
    <div class="cv-subheader">
        <p class="cv-h">Instructor: {{ $classes->instructor }}</p>
        <p class="cv-h">Schedule: {{ $classes->schedule }}</p>
    </div>

    {{-- Search Box --}}
    <div class="searchBox">
        <input type="text" name="search" placeholder="Quick Search...">
    </div>

    {{-- Add Student Icon --}}
    <div class="student-main-container">
    {{-- Check if logged in instructor is the one who added the class --}}
    @if(auth()->user()->name === $classes->added_by)
        <div class="iconBtbn" onclick="openAddStudentModal()">
            <span class="fa-stack fa-2x iconBtn">
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                <i class="fa-solid fa-layer-group fa-stack-1x layers-icon"></i>
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
            </span>
        </div>

        {{-- Student Table --}}
        @if(!$hasLockedAndSubmitted)
        <div class="student-wrapper">
            <form method="POST" action="{{ route('class.bulkRemove', $classes->id) }}">
            @csrf

            <table class="student-table-container">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all"> Select All
                        </th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @foreach ($classes_student as $student)
                    <tr>
                        <td>
                            <input type="checkbox"
                                class="student-checkbox"
                                name="selected_students[]"
                                value="{{ $student->studentID }}">
                        </td>
                        <td>{{ $student->studentID }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->gender }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->department }}</td>
                        <td>
                            <i class="fa-solid fa-trash delete-single"
                            style="cursor:pointer;color:red;"
                            data-remove-url="{{ route('class.remove', [$classes->id, $student->studentID]) }}"></i>
                        </td>
                    </tr>
                @endforeach

                {{-- BULK DELETE ROW --}}
                <tr id="bulk-delete-row" style="display:none;">
                    <td colspan="7" style="text-align:right;">
                        <button type="submit"
                                class="delete-selected"
                                style="background:none;border:none;color:red;cursor:pointer;">
                            <i class="fa-solid fa-trash"></i> Delete Selected
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            </form>
        </div>

        {{-- Single Delete Form --}}
        <form id="single-delete-form" method="POST" style="display:none;">
            @csrf
            @method('POST')
        </form>
        @endif
    @endif
</div>


        {{-- Student Grades --}}
        <div class="student-grades-container">
        <div class="student-grades-header">
            <h2>Student Grades</h2>
        </div>

            @php
        $groupedByDepartment = $filteredStudents->groupBy('department');
        $user = Auth::user();
        $userRoles = explode(',', $user->role);
        $isDean = in_array('dean', $userRoles);
        $isRegistrar = in_array('registrar', $userRoles);

        $educationGroups = [
            'Bachelor of Science in Education',
            'Bachelor of Elementary Education',
            'Bachelor of Secondary Education',
            'Education Major in Math',
            'Education Major in English',
            'BEED',
            'BSED',
        ];

        $financeAndOpsGroups = [
            'Bachelor of Science in Business Administration',
            'Bachelor of Science in Financial Management',
            'Bachelor of Science in Operating Management'
        ];

        $deptColors = [
            'Bachelor of Science in Computer Science' => 'green',
            'Bachelor of Science in Business Administration' => 'rgb(238, 191, 0)',
            'Bachelor of Arts in English Language Studies' => 'red',
            'Bachelor of Science in Education' => 'blue',
            'Bachelor of Science in Criminology' => 'violet',
            'Bachelor of Science in Social Work' => '#FF7F7F',
            'Education' => 'blue',
            'Bachelor of Science in Financial Management' => '#FFA500', // orange
            'Bachelor of Science in Operating Management' => '#1E90FF', // dodgerblue
            'Finance & Operations' => '#FFD700', // optional color
        ];

        // Map dean to the departments they can approve
        $deanVisibleDepartments = [];

        if ($user->department === 'Bachelor of Science in Education') {
            $deanVisibleDepartments = ['Education'];
        } elseif ($user->department === 'Bachelor of Science in Business Administration') {
            $deanVisibleDepartments = ['Bachelor of Science in Business Administration', 'Finance & Operations'];
        } else {
            $deanVisibleDepartments = [$user->department];
        }
        @endphp

        @foreach($groupedByDepartment as $dept => $studentsGroup)
            @php
                // Normalize department grouping
                $normalizedDept = in_array($dept, $educationGroups) ? 'Education' 
                                : (in_array($dept, $financeAndOpsGroups) ? 'Finance & Operations' : $dept);
                $deptColor = $deptColors[$dept] ?? ($deptColors[$normalizedDept] ?? 'black');

                // Check if logged-in dean can approve this department
                $sameDept = in_array($normalizedDept, $deanVisibleDepartments);

                $deptGrades = $finalGrades->whereIn('studentID', $studentsGroup->pluck('studentID')->map(fn($id) => (string)$id)->toArray());
                $departmentsToSubmit = ($normalizedDept === 'Education') ? $educationGroups 
                                    : (($normalizedDept === 'Finance & Operations') ? $financeAndOpsGroups : [$normalizedDept]);

                $allLocked = $deptGrades->every(fn($g) => $g->status === 'Locked');
                $allSubmitted = $deptGrades->every(fn($g) => $g->submit_status === 'Submitted');
                $showGradesTable = $allLocked && $allSubmitted;

                $allDeanApproved = $deptGrades->every(fn($g) => $g->dean_status === 'Confirmed');
                $hasRegistrarActionable = $deptGrades->contains(fn($g) => is_null($g->registrar_status));
                $showRegistrarSubmit = $isDean && $sameDept && $showGradesTable && $allDeanApproved && $hasRegistrarActionable;

                $pendingDeptApproval = $deptGrades->filter(fn($g) => is_null($g->dean_status) || $g->dean_status === 'Rejected')->count() > 0;
                $showDeanButtons = $isDean && $sameDept && $pendingDeptApproval;

                $hasPendingRegistrar = $deptGrades->contains(fn($g) => $g->registrar_status === 'Pending');
                $showRegistrarDecision = $isRegistrar && $hasPendingRegistrar;

                $firstGrade = $deptGrades->first();
            @endphp

                    {{-- Department Header --}}
                    <div class="student-grades-m-container">
                        <h3 class="student-department-header" style="color: {{ $deptColor }};">{{ $dept }}</h3>
                    </div>

                    {{-- Status Display --}}
                    <div class="status-container">
                        <span class="status-span">Status: {{ $firstGrade->status ?? '-' }}</span>
                        <span class="status-span">Submit to Dean Status: {{ $firstGrade->submit_status ?? '-' }}</span>
                        <span class="status-span">Dean Approval: {{ $firstGrade->dean_status ?? '-' }}</span>
                        <span class="status-span">Registrar Approval: {{ $firstGrade->registrar_status ?? '-' }}</span>
                    </div>

                    {{-- Grades Table --}}
                    @if($showGradesTable)
                        <div class="student-grades-wrapper">
                            <table class="student-grades-table-container">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Prelim</th>
                                        <th>Mid-Term</th>
                                        <th>Semi Finals</th>
                                        <th>Finals</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsGroup as $student)
                                        @php $grade = $deptGrades->firstWhere('studentID', $student->studentID); @endphp
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->department }}</td>
                                            <td>{{ $grade->prelim ?? '-' }}</td>
                                            <td>{{ $grade->midterm ?? '-' }}</td>
                                            <td>{{ $grade->semi_finals ?? '-' }}</td>
                                            <td>{{ $grade->final ?? '-' }}</td>
                                            <td>{{ $grade->remarks ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color:gray; font-style:italic; margin-top:10px;">
                            Grades are not yet locked and submitted for this department.
                        </p>
                    @endif
           @if(
            $showDeanButtons &&
            (
                is_null($firstGrade->dean_status) ||
                $firstGrade->dean_status === 'Returned'
            )
        )
    <p class="decision-header">Dean's Decision for: {{ $dept }}</p>

    <div class="decisionBtn-container">
        <form method="POST" action="{{ route('dean.decision') }}">
            @csrf

            <label style="margin-right: 15px;">
                <input type="checkbox" name="dean_status" value="Confirmed">
                <span class="decision-span">Confirmed</span>
            </label>

            <label>
                <input type="checkbox" name="dean_status" value="Returned">
                <span class="decision-span">Returned</span>
            </label>

            <br>

            <textarea name="dean_remarks"
                rows="2"
                cols="30"
                placeholder="Add a comment (only if returned)..."
                style="display:none;">
            </textarea>

            <br>

            <input type="hidden" name="department" value="{{ $dept }}">
            <input type="hidden" name="classID" value="{{ $classes->id }}">

            <button type="submit" class="deanDecisionBtn">
                Submit Decision for {{ $dept }}x
            </button>
        </form>
    </div>
@endif


            {{-- Submit to Registrar --}}
            @if($showRegistrarSubmit)
                <form method="POST" action="{{ route('registrar_submit_grades') }}">
                    @csrf
                    <input type="hidden" name="department" value="{{ $dept }}">
                    <input type="hidden" name="classID" value="{{ $classes->id }}">
                    <button type="submit" class="submitToRegistrarBTn">Submit to Registrar</button>
                </form>
            @endif

            {{-- Registrar Decision --}}
            @if($showRegistrarDecision)
                <div class="registrar-decision-container">
                    <h3 class="r-d-header">Registrar Decision for {{ $dept }}</h3>
                    <form method="POST" action="{{ route('registrar.decision') }}">
                        @csrf
                        <input type="hidden" name="department" value="{{ $dept }}">
                        <input type="hidden" name="classID" value="{{ $classes->id }}">
                        @if($showGradesTable)
                            @foreach($studentsGroup as $student)
                                <input type="hidden" name="grades[{{ $loop->index }}][studentID]" value="{{ $student->studentID }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][classID]" value="{{ $classes->id }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][prelim]" value="{{ $finalGrades->firstWhere('studentID', $student->studentID)->prelim ?? 0 }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][midterm]" value="{{ $finalGrades->firstWhere('studentID', $student->studentID)->midterm ?? 0 }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][semi_finals]" value="{{ $finalGrades->firstWhere('studentID', $student->studentID)->semi_finals ?? 0 }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][final]" value="{{ $finalGrades->firstWhere('studentID', $student->studentID)->final ?? 0 }}">
                                <input type="hidden" name="grades[{{ $loop->index }}][remarks]" value="{{ $finalGrades->firstWhere('studentID', $student->studentID)->remarks ?? '-' }}">
                            @endforeach
                        @endif

                        <label style="margin-right: 15px;">
                            <input type="radio" name="registrar_status" style="margin-left: 20px" value="Approved" required>
                            Approved
                        </label>
                        <label>
                            <input type="radio" name="registrar_status" value="Rejected" required>
                            Rejected
                        </label>

                        <textarea name="registrar_comment" id="registrar_comment" rows="2" cols="30" placeholder="Provide reason (required if Rejected)" style="display:none;"></textarea>
                        <br>

                        <button type="submit" class="registrarDecisionBtn" style="margin-top: 10px;">Submit Decision</button>
                    </form>
                </div>
            @endif

            <hr style="margin:40px 0;">
        @endforeach

    </div>
</div>

{{-- JS Functions --}}
<script>
function openAddStudentModal() { document.getElementById('addStudentModal').classList.add('show'); }
function closeAddStudentModal() { document.getElementById('addStudentModal').classList.remove('show'); }

let students = {!! json_encode($students) !!};
function filterStudents() {
    const input = document.getElementById("studentSearch").value.toLowerCase();
    const dropdown = document.getElementById("studentDropdown");
    dropdown.innerHTML = "";
    if (!input.trim()) { dropdown.classList.remove("show"); return; }

    let filtered = students.filter(student =>
        student.name.toLowerCase().includes(input) ||
        student.email.toLowerCase().includes(input) ||
        student.studentID.toString().includes(input) ||
        student.department.toLowerCase().includes(input)
    );

    filtered.forEach(student => {
        let option = document.createElement("div");
        option.textContent = `${student.studentID} - ${student.name} (${student.department})`;
        option.onclick = function () {
            document.getElementById("studentSearch").value = student.name;
            document.getElementById("student_id").value = student.studentID;
            document.getElementById("name").value = student.name;
            document.getElementById("gender").value = student.gender;
            document.getElementById("email").value = student.email;
            document.getElementById("department").value = student.department;
            dropdown.classList.remove("show");
        };
        dropdown.appendChild(option);
    });

    dropdown.classList.add("show");
}

document.addEventListener("click", function (e) {
    const input = document.getElementById("studentSearch");
    const dropdown = document.getElementById("studentDropdown");
    if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove("show");
});

// SweetAlert Delete
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const bulkDeleteRow = document.getElementById('bulk-delete-row');

    function updateBulkDeleteVisibility() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteRow.style.display = anyChecked ? 'table-row' : 'none';
    }

    selectAll.addEventListener('change', () => { checkboxes.forEach(cb => cb.checked = selectAll.checked); updateBulkDeleteVisibility(); });
    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkDeleteVisibility));

    // Bulk Delete
    const deleteSelected = document.querySelector('.delete-selected');
    if (deleteSelected) {
        deleteSelected.addEventListener('click', function () {
            const selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (!selectedIds.length) return;
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${selectedIds.length} students!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete them!'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulk-delete-form');
                    form.action = this.dataset.bulkUrl;
                    form.querySelectorAll('input[name="selected_students[]"]').forEach(el => el.remove());
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_students[]';
                        input.value = id;
                        form.appendChild(input);
                    });
                    form.submit();
                }
            });
        });
    }

    // Single Delete
document.querySelectorAll('.delete-single').forEach(icon => {
    icon.addEventListener('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "This student will be removed permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!'
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('single-delete-form');
                form.action = this.dataset.removeUrl;
                form.submit();
            }
        });
    });
});
});
</script>
@endsection
