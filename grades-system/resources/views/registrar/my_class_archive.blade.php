@extends('layouts.default')
@vite(['resources/css/my_class_archive.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
@section('content')

    <div class="my-class-archived-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>My Class Archived</span>
        </div>
        <div class="grade-archive-view">
            <div class="a-header-close">
                 <h2 class="archive-view-header">
                CS 10
                </h2>
            <span class="close-icon" id="closeGradeView">✖</span>
            </div>
            <h3 class="archive-view-subheader">
                Programming 1
            </h3>
            <div class="acad-details">
                <span class="instructor-label">Instructor:</span>
                <span class="instructor-value">John Mark Hondrada</span>
                <span class="schedule-label">Schedule:</span>
                <span class="schedule-value">Mon, Wed, Fri - 10:00 AM to 11:00 AM</span>
            </div>
            <div class="acad-container">
                <span class="acad-year-label">Academic Year:</span>
                <span class="acad-year-value">2025-2026</span>
                <span class="acad-period-label">Academic Period:</span>
                <span class="acad-period-value">Second Semester</span>
            </div>
            <div class="Grades">
                <h3 class="grades-header">
                    Grades
                </h3>
            </div>
            <div class="raw-columnBtn">
                <button type="button" class="rawBtn">
                    Show Raw Column
                </button>
            </div>
            <h3 class="department-header">
                Bachelor of Science in Computer Science
            </h3>
            <div class="search-student-archive">
                <input type="text" class="searchStudetn" name="searcHStudent" placeholder="Search Student...">
            </div>
            <div class="grades-wrapper-container">
                <table class="grades-table-container">
                    <thead>
                        <tr>
                            <th class="student-name-header">Student Name</th>
                            <th class="prelim-header">Prelim</th>
                            <th class="midraw-header">Midterm (Raw)</th>
                            <th class="midterm-header">Midterm</th>
                            <th class="midraw-header">Semi-Final (Raw)</th>
                            <th class="midraw-header">Semi Final</th>
                            <th class="finals-header">Final (Raw)</th>
                            <th class="final-grade-header">Finals</th>
                            <th class="remarks-header">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="student-name-cell">Alice Johnson</td>
                            <td class="prelim-cell">88</td>
                            <td class="prelim-cell">88</td>
                            <td class="prelim-cell">88</td>
                            <td class="prelim-cell">88</td>
                            <td class="midterm-cell">92</td>
                            <td class="finals-cell">85</td>
                            <td class="final-grade-cell">88.33</td>
                            <td class="remarks-cell">Passed</td>
                        </tr>
                        <tr>
                            <td class="student-name-cell">Bob Smith</td>
                            <td class="prelim-cell">76</td>
                            <td class="prelim-cell">76</td>
                            <td class="prelim-cell">76</td>
                            <td class="prelim-cell">76</td>
                            <td class="midterm-cell">81</td>
                            <td class="finals-cell">79</td>
                            <td class="final-grade-cell">78.67</td>
                            <td class="remarks-cell">Passed</td>
                        </tr>
                        <tr>
                            <td class="student-name-cell">Catherine Lee</td>
                            <td class="prelim-cell">90</td>
                            <td class="prelim-cell">90</td>
                            <td class="prelim-cell">90</td>
                            <td class="prelim-cell">90</td>
                            <td class="midterm-cell">94</td>
                            <td class="finals-cell">91</td>
                            <td class="final-grade-cell">91.67</td>
                            <td class="remarks-cell">Passed</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <h2 class="my-header">
            My Class Archived
        </h2>
        <div class="archive-folder-main-container">
            <div class="archive-folder-box">
                📁 Academic Year
            </div>
                <div class="year-box">
                    📁 2025-2026
                </div>
                    <div class="semester-box">
                        📁 First Semester
                    </div>
                    <div class="subject-box">
                        📁 Mathematics 101
                    </div>
        </div>
    </div>
<script>
    // Get elements
    const academicYearBox = document.querySelector('.archive-folder-box');
    const yearBox = document.querySelector('.year-box');
    const semesterBox = document.querySelector('.semester-box');
    const subjectBox = document.querySelector('.subject-box');
    const gradeArchiveView = document.querySelector('.grade-archive-view');
    const closeGradeView = document.getElementById('closeGradeView');

    // Step 1: Click Academic Year → Show Year Box
    academicYearBox.addEventListener('click', () => {
        yearBox.style.display = yearBox.style.display === 'block' ? 'none' : 'block';
        semesterBox.style.display = 'none';
        subjectBox.style.display = 'none';
        gradeArchiveView.style.display = 'none';
    });

    // Step 2: Click Year Box → Show Semester Box
    yearBox.addEventListener('click', () => {
        semesterBox.style.display = semesterBox.style.display === 'block' ? 'none' : 'block';
        subjectBox.style.display = 'none';
        gradeArchiveView.style.display = 'none';
    });

    // Step 3: Click Semester Box → Show Subject Box
    semesterBox.addEventListener('click', () => {
        subjectBox.style.display = subjectBox.style.display === 'block' ? 'none' : 'block';
        gradeArchiveView.style.display = 'none';
    });

    // Step 4: Click Subject Box → Show Grade Archive View
    subjectBox.addEventListener('click', () => {
        gradeArchiveView.style.display = gradeArchiveView.style.display === 'block' ? 'none' : 'block';
    });

    // Step 5: Close icon to hide grade archive view
    closeGradeView.addEventListener('click', () => {
        gradeArchiveView.style.display = 'none';
    });
</script>
@endsection