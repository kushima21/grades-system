@extends('layouts.default')
@vite(['resources/css/classes.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="classes-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Classes</span>
        </div>
        <h2 class="my-header">
            Classes
        </h2>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchClass" id="searchClass" placeholder="Quick Search...">
            </form>
        </div>
        <div class="classes-modal-container" id="classesModal">
            <h2 class="classes-modal-header">
                Create New Class
            </h2>
            <div class="classes-form-container">
               <form id="classForm" method="POST" action="{{ route('classes.create') }}">
                @csrf
                 @method('POST')
                <input type="hidden" name="class_id" id="class_id">
                    <div class="class-wrapper-container">
                       <div class="classes-info">
                            <label for="course_no">Course No: <em>*Example: GEC 001*</em></label>
                            <input type="text" name="course_no" id="courseInput" placeholder="Search for Course No...">
                            <!-- Dropdown for auto-complete -->
                            <div id="courseDropdown" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; display:none; position:absolute; background:white; z-index:1000;"></div>
                        </div>
                       <div class="classes-info" style="position: relative;">
                            <label for="instructor">Instructor</label>
                            <input type="text" name="instructor" id="instructorInput" placeholder="Search for an instructor...">
                            <!-- Dropdown for auto-complete -->
                            <div id="instructorDropdown" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; display:none; position:absolute; background:white; z-index:1000; width:100%;"></div>
                        </div>
                       <div class="classes-info">
                            <label for="descriptive_title">Descriptive Title:</label>
                            <input type="text" name="descriptive_title" id="descriptiveTitle" readonly>
                        </div>
                        <div class="classes-info">
                            <label for="academic_year">Academic Year:</label>
                            <select id="academic_year" name="academic_year">
                                    <option value="" disabled {{ old('academic_year') ? '' : 'selected' }}>Select Academic Year</option>
                                    @for ($year = 2024; $year <= date('Y') + 5; $year++)
                                    <option value="{{ $year }}-{{ $year + 1 }}" {{ old('academic_year') == "$year-$year+1" ? 'selected' : '' }}>
                                        {{ $year }}-{{ $year + 1 }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                        <div class="classes-info">
                            <label for="units">Units:</label>
                            <input type="text" name="units" id="units" readonly>
                        </div>
                        <div class="classes-info">
                            <label for="academic_period">Academic Period:</label>
                                <select id="academic_period" name="academic_period">
                                    <option value="" disabled {{ old('academic_period') ? '' : 'selected' }}>Select Academic Period</option>
                                    <option value="1st Semester" {{ old('academic_period') == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2nd Semester" {{ old('academic_period') == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="Summer" {{ old('academic_period') == 'Summer' ? 'selected' : '' }}>Summer</option>
                                </select>
                            </label>
                        </div>
                        <div class="classes-info">
                            <label for="schedule">Schedule: <em>*Example: Monday, Tuesday, Wednesday*</em></label>
                            <input type="text" name="schedule" placeholder="Enter Schedule Class...">
                        </div>
                        
                        <div class="classes-info">
                            <label for="schedule">Program: 
                                <em>*Example: BSCS, BEED, BSBA*</em>
                            </label>

                            <!-- Display selected departments -->
                            <input type="text" name="program" id="program" placeholder="Enter Program" required>
                        </div>
                         <input type="hidden" name="added_by" value="{{ Auth::user()->name ?? 'Test User' }}">

                       <div class="classes-info" style="display:none;">
                            <label for="status">Status:</label>
                            <select name="status" id="status" required>
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="classes-btn">
                            <button type="submit">Create</button>
                            <button type="button" id="closeModalBtn">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="classes-main-container-box">
           @php
                $role = strtolower(auth()->user()->role);
                $isDean = str_contains($role, 'dean');
            @endphp

            @if($isDean)
            <div class="iconBtbn">
                <span class="fa-stack fa-2x iconBtn">
                    <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                    <i class="fa-solid fa-layer-group fa-stack-1x layers-icon"></i>
                    <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
                </span>
            </div>
            @endif

            @foreach ($classes as $class)
            <div class="classes-box">
                <div class="classes-header">
                    <h3 class="my-c">{{ $class->course_no }}</h3>
                    <h3 class="subheader">{{ $class->descriptive_title }}</h3>
                </div>

                <div class="middle-c">
                    <span>School Year: {{ $class->academic_year }}</span>
                    <br>
                    <span>School Period: {{ $class->academic_period }}</span>
                    <br>
                    <span>Schedule: {{ $class->schedule }}</span>
                    <br>
                    <span>Status: {{ $class->status }}</span>
                    <br>
                    <span>{{ $class->department }}</span>
                </div>

                <div class="bottom-c">
                    <span>Instructor: {{ $class->instructor }}</span>
                </div>

                <div class="icon-container">
                   <span class="icon edit-icon" 
                        data-tooltip="Edit"
                        data-id="{{ $class->id }}"
                        data-course_no="{{ $class->course_no }}"
                        data-descriptive_title="{{ $class->descriptive_title }}"
                        data-units="{{ $class->units }}"
                        data-instructor="{{ $class->instructor }}"
                        data-academic_year="{{ $class->academic_year }}"
                        data-academic_period="{{ $class->academic_period }}"
                        data-schedule="{{ $class->schedule }}"
                        data-department="{{ $class->department }}"
                        data-status="{{ $class->status }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>


                   <span class="icon delete-icon" data-tooltip="Delete" data-id="{{ $class->id }}">
                        <i class="fa-solid fa-trash"></i>
                    </span>

                 
                        <a href="{{ route('class.show', $class->id) }}" class="icon view-icon" data-tooltip="View">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                  
                </div>
            </div>
            @endforeach
        </div>
        
    </div>
<script>
document.querySelector('.iconBtbn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'block';
});

document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'none';
});
document.querySelectorAll('.edit-icon').forEach(editBtn => {
    editBtn.addEventListener('click', function () {

        // Open modal
        document.getElementById('classesModal').style.display = 'block';

        // Change header
        document.querySelector('.classes-modal-header').innerText = "Edit Class";

        // Change form action to UPDATE route
        const classId = this.dataset.id;
        document.getElementById('classForm').action = `/classes/edit/${classId}`;
        document.getElementById('class_id').value = classId;

        // Fill form inputs
        document.getElementById('courseInput').value = this.dataset.course_no;
        document.getElementById('descriptiveTitle').value = this.dataset.descriptive_title;
        document.getElementById('units').value = this.dataset.units;
        document.getElementById('instructorInput').value = this.dataset.instructor;
        document.getElementById('academic_year').value = this.dataset.academic_year;
        document.getElementById('academic_period').value = this.dataset.academic_period;
        document.querySelector('input[name="schedule"]').value = this.dataset.schedule;
        document.getElementById('department').value = this.dataset.department;
        document.getElementById('status').value = this.dataset.status;

        // Change submit button text
        document.querySelector('.classes-btn button[type="submit"]').innerText = "Update";
    });
});

// Close modal
document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'none';
});
</script>

<script>
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
</script>
<script>
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
document.addEventListener('click', function(e){
    if (!courseDropdown.contains(e.target) && e.target !== courseInput){
        courseDropdown.style.display = 'none';
    }
});
</script>
<script>
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
document.addEventListener('click', function(e){
    if (!instructorDropdown.contains(e.target) && e.target !== instructorInput){
        instructorDropdown.style.display = 'none';
    }
});
</script>
<script>
    const select = document.getElementById('departmentSelect');
    const container = document.getElementById('selectedDepartmentsContainer');
    const hiddenInput = document.getElementById('departmentHiddenInput');

    let selectedDepartments = [];

    // Add department when selected
    select.addEventListener('change', () => {
        const value = select.value;

        // Avoid duplicates
        if (value && !selectedDepartments.includes(value)) {
            selectedDepartments.push(value);
            renderSelectedDepartments();
        }
        select.value = "";
    });

    // Render pills UI
    function renderSelectedDepartments() {
        container.innerHTML = "";

        selectedDepartments.forEach((dept, index) => {
            const pill = document.createElement('div');
            pill.style.cssText = `
                padding: 5px 10px;
                background: #e3e3e3;
                border-radius: 6px;
                display: flex;
                align-items: center;
                gap: 6px;
            `;
            pill.innerHTML = `
                <span>${dept}</span>
                <button type="button" data-index="${index}" 
                    style="
                        border:none; 
                        background:red; 
                        color:white; 
                        width:18px; 
                        height:18px; 
                        border-radius:50%; 
                        cursor:pointer;
                        font-size:12px;
                        line-height:16px;
                        text-align:center;
                    ">×</button>
            `;

            container.appendChild(pill);
        });

        // Update hidden input (for form submission)
        hiddenInput.value = selectedDepartments.join(",");

        // Add remove event
        container.querySelectorAll("button").forEach(btn => {
            btn.addEventListener("click", () => {
                const index = btn.getAttribute("data-index");
                selectedDepartments.splice(index, 1);
                renderSelectedDepartments();
            });
        });
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteIcons = document.querySelectorAll('.delete-icon');

    deleteIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const classId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form dynamically to send POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/classes/delete/${classId}`;
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection