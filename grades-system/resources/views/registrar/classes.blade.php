@extends('layouts.default')
@vite(['resources/css/classes.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
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
                <form method="POST" action="">
                    <div class="class-wrapper-container">
                        <div class="classes-info">
                            <label for="course_no">Course No: <em>*Example: GEC 001*</em></label>
                            <input type="text" placeholder="Search for Course No...">
                        </div>
                        <div class="classes-info">
                            <label for="Instructor">Instructor</label>
                            <input type="text" placeholder="Search for an instructor...">
                        </div>
                        <div class="classes-info">
                            <label for="descriptive_title">Descriptive Title:</label>
                            <input type="text">
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
                            <input type="text">
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
                            <input type="text" placeholder="Enter Schedule Class...">
                        </div>
                           <div class="classes-info">
                            <label for="schedule">Set: <em>*Example: Set A, Set B, CS, BSDE*</em></label>
                            <input type="text" placeholder="Enter Schedule Class...">
                        </div>
                        <div class="classes-btn">
                            <button type="submit" name="submit">Create</button>
                            <button type="button" id="closeModalBtn">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="classes-main-container-box">
            <div class="iconBtbn">
                <span class="fa-stack fa-2x iconBtn">
                    <!-- Circle background -->
                <i class="fa-solid fa-circle fa-stack-2x circle-bg"></i>
                    <!-- Layers icon -->
                <i class="fa-solid fa-layer-group fa-stack-1x layers-icon"></i>
                    <!-- Plus icon -->
                <i class="fa-solid fa-plus fa-stack-1x plus-icon"></i>
                </span>
            </div>
            <div class="classes-box">
                <div class="classes-header">
                    <h3 class="my-c">CS-10</h3>
                    <h3 class="subheader">Programming 1</h3>
                </div>
                <div class="middle-c">
                    <span>School Year: 2025-2026</span>
                    <br>
                    <span>School Period: 1st Semester</span>
                    <br>
                    <span>Schedule: 8:00am-10:00am MTH</span>
                    <br>
                    <span>Status: Active</span>
                </div>
                <div class="bottom-c">
                    <span>Instructor: John Mark Hondrada</span>
                    <a href="#">
                        <i class="fa-solid fa-right-from-bracket"></i> 
                    </a>
                </div>
            </div>
            
        </div>
    </div>
<script>
document.querySelector('.iconBtbn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'block';
});

document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('classesModal').style.display = 'none';
});
</script>
@endsection