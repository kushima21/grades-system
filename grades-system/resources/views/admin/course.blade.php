@extends('layouts.default')
@vite(['resources/css/course.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <form onsubmit="return false;">
                <input type="text" id="searchCourse" name="searchCourse" placeholder="Quick Search">
            </form>
        </div>
        <div class="course-modal-container">
            <h2 class="course-modal-header">
                Create New Course
            </h2>
            <div class="course-form-container">
                <form id="courseForm" method="POST" action="{{ route('course.store') }}">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    <div class="form-info">
                        <label>Course No:</label>
                        <input type="text" id="course_no" name="course_no" required>
                    </div>

                    <div class="form-info">
                        <label>Descriptive Title:</label>
                         <input type="text" id="descriptive_title" name="descriptive_title" required>
                    </div>

                    <div class="form-info">
                        <label>Course Components:</label>

                        <!-- Hidden input to store selected dropdown value -->
                         <input type="hidden" id="course_components" name="course_components">

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

                    <script>
                        document.querySelectorAll(".dropdown-content div").forEach(item => {
                            item.addEventListener("click", function () {
                                let selectedValue = this.getAttribute("data-value");
                                document.getElementById("course_components").value = selectedValue;
                                document.querySelector(".dropbtn").textContent = selectedValue;
                            });
                        });
                    </script>

                    <div class="form-info">
                        <label>Units:</label>
                       <input type="number" id="units" name="units" required>
                    </div>

                    <div class="course-modalBtn">
                        <button type="submit" id="submitBtn">Create</button>
                        <button type="button" id="closeModal">Close</button>
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
                    @forelse ($courses ?? [] as $course)
                        <tr>
                            <td>{{ $course->course_no }}</td>
                            <td>{{ $course->descriptive_title }}</td>
                            <td>{{ $course->course_components }}</td>
                            <td>{{ $course->units }}</td>
                            <td>{{ $course->created_at ? $course->created_at->format('Y-m-d') : '' }}</td>

                            <td>
                                <div class="c-action">

                                    <!-- Edit Icon -->
                                   <i class="fa-solid fa-pen-to-square editBtn"
                                        style="cursor:pointer; color:blue;"
                                        data-id="{{ $course->id }}"
                                        data-course_no="{{ $course->course_no }}"
                                        data-title="{{ $course->descriptive_title }}"
                                        data-components="{{ $course->course_components }}"
                                        data-units="{{ $course->units }}">
                                    </i>

                                    <!-- Delete Icon -->
                                    <i class="fa-solid fa-trash deleteBtn"
                                    data-id="{{ $course->id }}"
                                    style="cursor:pointer; color:red;"></i>

                                    <!-- Hidden Delete Form -->
                                    <form id="delete-form-{{ $course->id }}"
                                        action="{{ route('course.destroy', $course->id) }}"
                                        method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">No courses found.</td>
                        </tr>
                    @endforelse
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
    const form = document.getElementById('courseForm');
    const methodInput = document.getElementById('formMethod');
    const submitBtn = document.getElementById('submitBtn');

    // 🔹 CLOSE MODAL
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // 🔹 OPEN MODAL FOR CREATE (icon button)
    iconBtn.addEventListener('click', () => {
        modal.style.display = 'block';

        // Reset form to CREATE mode
        form.action = "{{ route('course.store') }}";
        methodInput.value = "POST";
        submitBtn.textContent = "Create";

        form.reset();
        document.querySelector('.dropbtn').textContent = "Select Components";
    });

    // 🔹 OPEN MODAL FOR EDIT
    document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', function () {

            modal.style.display = 'block';

            let id = this.dataset.id;
            let courseNo = this.dataset.course_no;
            let title = this.dataset.title;
            let components = this.dataset.components;
            let units = this.dataset.units;

            // Prefill form
            document.getElementById('course_no').value = courseNo;
            document.getElementById('descriptive_title').value = title;
            document.getElementById('course_components').value = components;
            document.querySelector('.dropbtn').textContent = components;
            document.getElementById('units').value = units;

            // Change form to UPDATE mode
            form.action = "/course/" + id + "/update";
            methodInput.value = "PUT";
            submitBtn.textContent = "Update";
        });
    });
</script>

<script>
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function () {

            let id = this.getAttribute('data-id');

            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to delete this course?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });

        });
    });
</script>
<script>
    const searchInput = document.getElementById('searchCourse');
    const table = document.querySelector('.course-table-container tbody');
    const rows = table.querySelectorAll('tr');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;

            // Skip empty row (No courses found)
            if (row.querySelector('td[colspan]')) return;

            // Check each cell
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });

            // Show or hide row
            row.style.display = match ? '' : 'none';
        });
    });
</script>

@endsection