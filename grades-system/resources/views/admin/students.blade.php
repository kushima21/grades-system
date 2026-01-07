@extends('layouts.default')
@vite(['resources/css/students.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="student-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Students Account</span>
        </div>
        <h2 class="my-header">
            Students Account
        </h2>

         <div class="student-modal-container">
            <h3 class="student-header">
                Student Information
            </h3>
            <div class="student-account-form-container">
                <form method="" action="">
                    @csrf
                    <div class="student-form-box-container">
                            <div class="form-info">
                                <label>First Name:</label>
                                <input type="text" name="fname" id="fname" placeholder="Enter Student First Name...">
                            </div>
                            <div class="form-info">
                                <label>School ID:</label>
                                <input type="text" name="studentID" id="studentID" placeholder="Enter School ID...">
                            </div>
                            <div class="form-info">
                                <label>Middle Name:</label>
                                <input type="text" name="mname" id="mname" placeholder="Enter Student Middle Name...">
                            </div>
                            <div class="form-info">
                                <label>Department:</label>
                                <select name="department">
                                    <option value="">Select Department</option>
                                    <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                                    <option value="Bachelor of Business in Administration">Bachelor of Business in Administration</option>
                                    <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
                                    <option value="Bachelor of Science in Social Studies">Bachelor of Science in Social Studies</option>
                                    <option value="Bachelor of Arts in English Studies">Bachelor of Arts in English Studies</option>
                                    <option value="Bachelor of Science in Criminology">Bachelor of Science in Criminology</option>
                                </select>
                            </div>
                            <div class="form-info">
                                <label>Last Name:</label>
                                <input type="text" name="lname" id="lname" placeholder="Enter Student Last Name...">
                            </div>
                            <div class="form-info">
                                <label>Abbreviation:</label>
                                <input type="text" name="lname" id="lname" readonly>
                            </div>
                            <div class="form-info">
                                <label>Gender:</label>
                                <select name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="MALE">MALE</option>
                                    <option value="FEMALE">FEMALE</option>
                                </select>
                            </div>
                            <div class="form-info">
                                <label>Year Level:</label>
                                <input type="text" name="yearLevel" id="yearLevel" placeholder="Enter Year Level...">
                            </div>
                            <div class="form-info">
                                <label>Nationality:</label>
                                <input type="text" name="nationality" id="nationality" placeholder="Enter Nationality...">
                            </div>
                            <div class="form-info">
                                <label>Batch Year:</label>
                                <input type="text" name="batchYear" id="batchYear" placeholder="Enter Batch Year...">
                            </div>
                    </div>
                    <div class="studentBtn">
                        <button>Submit</button>
                        <button>Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchUser" id="searchUser" placeholder="Quick Search...">
            </form>
        </div>
        
        <div class="student-table-wrapper-container">
             <div class="iconBtbn">
                <span class="fa-stack fa-2x iconBtn">
                    <!-- Circle background -->
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                    <!-- Layers icon -->
                <i class="fa-solid fa-book-open fa-stack-1x layers-icon"></i>
                    <!-- Plus icon -->
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
                </span>
            </div>
            <table class="student-table-container">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Year Level</th>
                        <th>Batch Year</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2021001</td>
                        <td style="font-style: italic">johnhondrada@ckcm.edu.ph</td>
                        <td>MALE</td>
                        <td>Bachelor of Science in Computer Sciencea</td>
                        <td>1</td>
                        <td>2019 - 2020</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection