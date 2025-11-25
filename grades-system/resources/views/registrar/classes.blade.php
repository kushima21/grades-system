@extends('layouts.default')
@vite(['resources/css/classes.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@section('content')

<div class="classes-main-container">

    {{-- Breadcrumb --}}
    <div class="span">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Classes</span>
    </div>

    <h2 class="my-header">Classes</h2>

    {{-- Search Bar --}}
    <div class="search-bar">
        <input type="text" name="searchClass" id="searchClass" placeholder="Quick Search...">
    </div>

    {{-- Modal --}}
    <div class="classes-modal-container" id="classesModal">
        <h2 class="classes-modal-header">Create New Class</h2>

        <div class="classes-form-container">
            <form method="POST" action="{{ route('CreateClass') }}">
                @csrf

                <div class="class-wrapper-container">

                    {{-- COURSE NUMBER --}}
                    <div class="classes-info">
                        <label>Course No: <em>*Example: GEC 001*</em></label>
                        <input type="text" id="courseInput" name="course_no" placeholder="Search for Course No...">
                        <div id="courseDropdown" class="dropdown-box"></div>
                    </div>

                    {{-- INSTRUCTOR --}}
                    <div class="classes-info" style="position: relative;">
                        <label>Instructor</label>
                        <input type="text" id="instructorInput" name="instructor" placeholder="Search for an instructor...">
                        <div id="instructorDropdown" class="dropdown-box"></div>
                    </div>

                    {{-- DESCRIPTIVE TITLE --}}
                    <div class="classes-info">
                        <label>Descriptive Title:</label>
                        <input type="text" id="descriptiveTitle" name="descriptive_title" readonly>
                    </div>

                    {{-- ACADEMIC YEAR --}}
                    <div class="classes-info">
                        <label>Academic Year:</label>
                        <select id="academic_year" name="academic_year">
                            <option disabled selected>Select Academic Year</option>
                            @for ($year = 2024; $year <= date('Y') + 5; $year++)
                                <option value="{{ $year }}-{{ $year + 1 }}">
                                    {{ $year }}-{{ $year + 1 }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- UNITS --}}
                    <div class="classes-info">
                        <label>Units:</label>
                        <input type="text" id="units" name="units" readonly>
                    </div>

                    {{-- ACADEMIC PERIOD --}}
                    <div class="classes-info">
                        <label>Academic Period:</label>
                        <select id="academic_period" name="academic_period">
                            <option disabled selected>Select Academic Period</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>

                    {{-- SCHEDULE --}}
                    <div class="classes-info">
                        <label>Schedule:</label>
                        <input type="text" name="schedule" placeholder="Enter Schedule...">
                    </div>

                    {{-- DEPARTMENT MULTI SELECT --}}
                    <div class="classes-info">
                        <label>Department: <em>*Example: BSCS, BEED, BSBA*</em></label>

                        <div id="selectedDepartmentsContainer" style="display:flex; gap:5px; flex-wrap:wrap;"></div>

                        <select id="departmentSelect">
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->department_code }}">{{ $dept->department_code }}</option>
                            @endforeach
                        </select>

                        {{-- Hidden field that will store array of departments --}}
                        <input type="hidden" name="department" id="departmentHidden">
                    </div>

                 

                    {{-- STATUS (required in controller but missing) --}}
                    <input type="hidden" name="status" value="Active">

                    {{-- BUTTONS --}}
                    <div class="classes-btn">
                        <button type="submit">Create</button>
                        <button type="button" id="closeModalBtn">Close</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- SAMPLE CLASS CARD --}}
    <div class="classes-main-container-box">
        <div class="iconBtbn">
            <span class="fa-stack fa-2x iconBtn">
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                <i class="fa-solid fa-layer-group fa-stack-1x layers-icon"></i>
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
            </span>
        </div>

        <div class="classes-box">
            <div class="classes-header">
                <h3>CS-10</h3>
                <h3 class="subheader">Programming 1</h3>
            </div>

            <div class="middle-c">
                <span>School Year: 2025-2026</span><br>
                <span>School Period: 1st Semester</span><br>
                <span>Schedule: 8:00am-10:00am MTH</span><br>
                <span>Status: Active</span><br>
                <span>BSCS</span>
            </div>

            <div class="bottom-c">
                <span>Instructor: John Mark Hondrada</span>
            </div>

            <div class="icon-container">
                <span class="icon edit-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                <span class="icon delete-icon"><i class="fa-solid fa-trash"></i></span>
                <a href="#" class="icon view-icon"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </div>

</div>

{{-- =================== MODAL SCRIPT =================== --}}
<script>

// =========================
//  OPEN & CLOSE MODAL
// =========================
document.querySelector('.iconBtbn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'block';
});

document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'none';
});


// =========================
//  MULTI-SELECT DEPARTMENT
// =========================
const select = document.getElementById('departmentSelect');
const container = document.getElementById('selectedDepartmentsContainer');
let selectedDepartments = [];

function renderDepartments() {
    container.innerHTML = '';
    selectedDepartments.forEach((dept, index) => {
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.alignItems = 'center';
        div.style.padding = '2px 6px';
        div.style.background = '#e0e0e0';
        div.style.borderRadius = '4px';

        const span = document.createElement('span');
        span.textContent = dept;
        span.style.marginRight = '5px';

        const closeBtn = document.createElement('button');
        closeBtn.textContent = 'x';
        closeBtn.style.border = 'none';
        closeBtn.style.background = 'transparent';
        closeBtn.style.cursor = 'pointer';
        closeBtn.addEventListener('click', () => {
            selectedDepartments.splice(index, 1);
            renderDepartments();
        });

        div.appendChild(span);
        div.appendChild(closeBtn);
        container.appendChild(div);
    });
}

select.addEventListener('change', () => {
    const value = select.value;
    if (value && !selectedDepartments.includes(value)) {
        selectedDepartments.push(value);
        renderDepartments();
    }
    select.value = '';
});


// =========================
//  COURSE AUTO-COMPLETE
// =========================
const courseInput = document.getElementById('courseInput');
const courseDropdown = document.getElementById('courseDropdown');
const descriptiveTitleInput = document.getElementById('descriptiveTitle');
const unitsInput = document.getElementById('units');

courseInput.addEventListener('keyup', function() {
    const query = this.value;

    if (query.length < 1) {
        courseDropdown.style.display = 'none';
        return;
    }

    fetch(`/courses/search?query=${query}`)
        .then(response => response.json())
        .then(data => {
            courseDropdown.innerHTML = '';

            if (data.length > 0) {
                data.forEach(course => {
                    const div = document.createElement('div');
                    div.textContent = `${course.course_no} - ${course.descriptive_title} (${course.units} units)`;
                    div.style.padding = '5px';
                    div.style.cursor = 'pointer';

                    div.addEventListener('click', () => {
                        courseInput.value = course.course_no;
                        descriptiveTitleInput.value = course.descriptive_title;
                        unitsInput.value = course.units;
                        courseDropdown.style.display = 'none';
                    });

                    courseDropdown.appendChild(div);
                });

                courseDropdown.style.display = 'block';
            } else {
                courseDropdown.style.display = 'none';
            }
        });
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!courseDropdown.contains(e.target) && e.target !== courseInput) {
        courseDropdown.style.display = 'none';
    }
});


// =========================
//  INSTRUCTOR AUTO-COMPLETE
// =========================
const instructorInput = document.getElementById('instructorInput');
const instructorDropdown = document.getElementById('instructorDropdown');

instructorInput.addEventListener('keyup', function() {
    const query = this.value;

    if (query.length < 1) {
        instructorDropdown.style.display = 'none';
        return;
    }

    fetch(`/instructors/search?query=${query}`)
        .then(response => response.json())
        .then(data => {
            instructorDropdown.innerHTML = '';

            if (data.length > 0) {
                data.forEach(inst => {
                    const div = document.createElement('div');
                    div.textContent = inst.name;
                    div.style.padding = '5px';
                    div.style.cursor = 'pointer';

                    div.addEventListener('click', () => {
                        instructorInput.value = inst.name;
                        instructorDropdown.style.display = 'none';
                    });

                    instructorDropdown.appendChild(div);
                });

                instructorDropdown.style.display = 'block';
            } else {
                instructorDropdown.style.display = 'none';
            }
        });
});

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!instructorDropdown.contains(e.target) && e.target !== instructorInput) {
        instructorDropdown.style.display = 'none';
    }
});

</script>
@endsection
