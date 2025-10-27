@extends('layouts.default')
@vite(['resources/css/course.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@section('content')
    <div class="course-main-container">
       <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Courses</span>
        </div>
        <h2 class="course-header">
            Courses
        </h2>
        <div class="search-course-container">
            <form method="" action="">
                <input type="text" name="searchCourse" placeholder="Quick Search">
            </form>
        </div>
        <div class="course-modal-container">
            <h2 class="course-modal-header">
                Create New Course
            </h2>
            <div class="course-form-container">
                <form method="POST" action="">
                    <div class="form-info">
                        <label for="course_no">
                            Course No:
                        </label>
                        <input type="text" >
                    </div>
                    <div class="form-info">
                        <label for="descriptive_title">
                            Descriptive Title:
                        </label>
                        <input type="text">
                    </div>
                    <div class="form-info">
                        <label for="course_components">Course Components:</label>
                            <div class="dropdown">
                                <button type="button" class="dropbtn">Select Components</button>
                                    <div class="dropdown-content">
                                        <div data-value="General Education">General Education</div>
                                        <div data-value="Major/Specialization">Major/Specialization</div>
                                        <div data-value="Physical Education">Physical Education</div>
                                        <div data-value="NSTP">NSTP</div>
                                        <div data-value="Religious Studies">Religious Studies</div>
                                        <div data-value="Professional Courses">Professional Courses</div>
                                        <div data-value="Professional Education">Professional Education</div>
                                        <div data-value="Elective Courses">Elective Courses</div>
                                        <div data-value="CS Electives">CS Electives</div>
                                        <div data-value="BSBA Electives">BSBA Electives</div>
                                        <div data-value="Allied Course">Allied Course</div>
                                        <div data-value="Allied">Allied</div>
                                        <div data-value="EDUC 100">EDUC 100</div>
                                        <div data-value="EdEng">EdEng</div>
                                        <div data-value="EdMath">EdMath</div>
                                        <div data-value="Student Formation Course">Student Formation Course</div>
                                        <div data-value="Business Administration Core Courses">Business Administration Core Courses</div>
                                        <div data-value="Common Business Management Education Courses">Common Business Management Education Courses</div>
                                        <div data-value="Cognates">Cognates</div>
                                        <div data-value="Mandated Courses">Mandated Courses</div>
                                        <div data-value="FLE">FLE</div>
                                        <div data-value="CS">CS</div>
                                        <div data-value="CRIM">CRIM</div>
                                        <div data-value="BA">BA</div>
                                        <div data-value="BSBA">BSBA</div>
                                        <div data-value="BSMath">BSMath</div>
                                        <div data-value="OJT">OJT</div>
                                        <div data-value="ELS">ELS</div>
                                        <div data-value="GMRC">GMRC</div>
                                        <div data-value="HBO">HBO</div>
                                        <div data-value="MTB">MTB</div>
                                        <div data-value="Other Courses">Other Courses</div>
                                    </div>
                            </div>   
                    </div>
                    <div class="form-info">
                        <label for="units">
                            Units:
                        </label>
                        <input type="number">
                    </div>
                    <div class="course-modalBtn">
                        <button type="submit" name="submit">
                            Create
                        </button>
                        <button type="button" id="closeModal">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="course-table-wrapper-container">
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
            <table class="course-table-container">
                <thead>
                    <tr>
                        <th>Course No</th>
                        <th>Descriptive Title</th>
                        <th>Course Components</th>
                        <th>Units</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CS-10</td>
                        <td>Programming 1</td>
                        <td>Major</td>
                        <td>3</td>
                        <td>2025-23-10</td>
                        <td>
                            <div class="c-action">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <i class="fa-solid fa-trash"></i>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<script>
document.querySelectorAll('.dropdown').forEach(drop => {
  const btn = drop.querySelector('.dropbtn');
  const content = drop.querySelector('.dropdown-content');
  const hiddenInput = document.getElementById('course_components'); // hidden input

  btn.addEventListener('click', () => {
    content.style.display = content.style.display === "block" ? "none" : "block";
  });

  content.querySelectorAll('div').forEach(option => {
    option.addEventListener('click', () => {
      btn.textContent = option.textContent;
      btn.setAttribute("data-value", option.dataset.value);
      hiddenInput.value = option.dataset.value; // set value to hidden input
      content.style.display = "none";
    });
  });

  // close when click outside
  document.addEventListener('click', function(e) {
    if (!drop.contains(e.target)) {
      content.style.display = "none";
    }
  });
});
</script>
<script>
    const iconBtn = document.querySelector('.iconBtbn');
    const modal = document.querySelector('.course-modal-container');
    const closeBtn = document.getElementById('closeModal');

    // 🔹 Show modal when icon is clicked
    iconBtn.addEventListener('click', () => {
      modal.style.display = 'block';
    });

    // 🔹 Hide modal when Close is clicked
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    // 🔹 Optional: Close modal when clicking outside of it
    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
</script>
@endsection