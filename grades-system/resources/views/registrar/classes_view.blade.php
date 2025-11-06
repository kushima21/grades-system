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
    <h2 class="my-header">CS-10</h2>
    <span class="class-header">Programming 1</span>

    <div class="cv-subheader">
        <p class="cv-h">Academic Year: 2025-2026</p>
        <p class="cv-h">Academic Period: First Semester</p>
    </div>
    <div class="cv-subheader">
        <p class="cv-h">Instructor: John Mark Hondrada</p>
        <p class="cv-h">Schedule: 10:00am - 12:00pm Monday/Tuesday/Wednesday</p>
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
            <form method="POST" action="">
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
@endsection
