@extends('layouts.default')
@vite(['resources/css/classes_view.css', 'resources/js/app.js'])

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

@section('content')
<div class="classes-view-main-container">
    <!-- Breadcrumb -->
    <div class="span">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Classes</span>
        <span>></span>
        <span>Classes Details</span>
    </div>

    <!-- Class Info -->
    <h2 class="my-header">{{ $classes->course_no}}</h2>
    <span class="class-header">{{ $classes->descriptive_title}}</span>

    <div class="cv-subheader">
        <p class="cv-h">Academic Year: {{ $classes->academic_year}}</p>
        <p class="cv-h">Academic Period: {{ $classes->academic_period}}</p>
    </div>
    <div class="cv-subheader">
        <p class="cv-h">Instructor: {{ $classes->instructor}}</p>
        <p class="cv-h">Schedule: {{ $classes->schedule}}</p>
    </div>

    <!-- ADD STUDENT MODAL -->
    <div class="add-student-modal-container" id="addStudentModal">
        <h3 class="add-header">Add Student</h3>

        <!-- CSV Upload Section -->
        <div class="csv-container">
            <form action="" method="POST">
                @csrf 
                <input type="file" name="students_csv" id="students_csv" accept="" required>
                <button name="submit" class="save-btn">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    Add Multiple Students
                </button>
            </form>
            <p class="csv-p">or add student individually</p>
        </div>

        <!-- Manual Add Form -->
        <div class="class-view-form-container">
            <form method="POST" action="{{ route('class.addstudent', $class) }}">
                @csrf
                <div class="info-add">
                    <label for="studentSearch">Find Student</label>
                    <input type="text" id="studentSearch" class="form-control" 
                           oninput="filterStudents()" placeholder="Search for a student...">
                    <div id="studentDropdown" class="dropdown-menu"></div>
                </div>

                <div class="info-add">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           placeholder="Student Name" required readonly>
                </div>

                <div class="info-add">
                    <label for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" class="form-control" 
                           placeholder="Student ID" required readonly>
                </div>

                <div class="info-add">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Student Email" required readonly>
                </div>

                <div class="info-add">
                    <label for="gender">Gender</label>
                    <input type="text" name="gender" id="gender" class="form-control" 
                           placeholder="Student Gender" required readonly>
                </div>

                <div class="info-add">
                    <label for="department">Department</label>
                    <input type="text" name="department" id="department" class="form-control" 
                           placeholder="Student Department" required readonly>
                </div>

                <!-- Buttons -->
                <div class="add-studentBtn">
                    <button type="submit" name="submit">
                        <i class="fa-solid fa-file-arrow-up"></i>
                        Add Student
                    </button>
                    <button type="button" onclick="closeAddStudentModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Box -->
    <div class="searchBox">
        <input type="text" name="search" placeholder="Quick Search...">
    </div>

    <!-- Add Student Icon -->
    <div class="student-main-container">
        <div class="iconBtbn" onclick="openAddStudentModal()">
            <span class="fa-stack fa-2x iconBtn">
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                <i class="fa-solid fa-layer-group fa-stack-1x layers-icon"></i>
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
            </span>
        </div>
        <div class="student-wrapper">
            <table class="student-table-container">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Department</th>
                    </tr>
                </thead>
                 @foreach ($classes_student as $classes_students)
                <tbody>
                    <tr>
                        <td>{{ $classes_students->studentID }}</td>
                        <td>{{ $classes_students->name }}</td>
                        <td>{{ $classes_students->gender }}</td>
                        <td>{{ $classes_students->email }}</td>
                        <td>{{ $classes_students->department }}</td>
                    </tr>
                </tbody>
                @endforeach
            </table>
        </div>
    </div>
</div>

<!-- Modal Functions -->
<script>
function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('show');
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('show');
}
</script>
  <script>
        // ✅ Students data from Laravel
    let students = {!! json_encode($students) !!};

    function filterStudents() {
        const input = document.getElementById("studentSearch").value.toLowerCase();
        const dropdown = document.getElementById("studentDropdown");
        dropdown.innerHTML = ""; // Clear previous results

        if (input.trim() === "") {
            dropdown.classList.remove("show");
            return;
        }

        let filtered = students.filter(student =>
            student.name.toLowerCase().includes(input) ||
            student.email.toLowerCase().includes(input) ||
            student.studentID.toString().includes(input) ||
            student.department.toLowerCase().includes(input)
        );

        if (filtered.length === 0) {
            dropdown.classList.remove("show");
            return;
        }

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

    // Hide dropdown when clicking outside
    document.addEventListener("click", function (e) {
        const input = document.getElementById("studentSearch");
        const dropdown = document.getElementById("studentDropdown");
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove("show");
        }
    });
    </script>
<script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const bulkDeleteContainer = document.getElementById('bulkDeleteContainer');

    // Function to check if any checkbox is selected
    function toggleBulkDeleteButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        bulkDeleteContainer.style.display = anyChecked ? 'block' : 'none';
    }

    // ✅ Toggle all checkboxes when Select All is clicked
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkDeleteButton();
    });

    // ✅ Listen to each checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteButton);
    });
</script>
@endsection
