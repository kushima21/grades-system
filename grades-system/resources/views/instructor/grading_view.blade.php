@extends('layouts.default')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
@vite(['resources/css/grading_view.css', 'resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $activeTerm = session('active_term');
@endphp
@section('content')
<div class="grading-view-main-container">

    {{-- NAV PATH --}}
    <div class="span">
        <span>Admin</span>
        <span>></span>
        <span>Manage</span>
        <span>></span>
        <span>Grading & Score</span>
    </div>

    <h2 class="my-header">Grading & Score</h2>
    @php
    $dept = 'Bachelor of Science in Computer Science'; // dynamic if needed
    $deptStatus = $departmentStatus[$dept] ?? null;
@endphp
    {{-- CLASS VALIDATION --}}
    @if (!isset($class) || !$class)
        <script>
            console.warn('Class not found. Redirecting...');
            window.location.href = "{{ route('instructor.grading&score') }}";
        </script>
    @else

    <div class="c-table-main-container">

        {{-- ================= TABS (Terms) ================= --}}
        <div class="c-table-list-container">
            @php
                $terms = ($academic_period === 'Summer')
                    ? ['Midterm', 'Finals']
                    : ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];
            @endphp

            @foreach ($terms as $term)
                <button type="button" class="grading-tab" id="tab-{{ $term }}">
                    {{ $term }}
                </button>
            @endforeach
        </div>

        {{-- ================= GRADING FORMS ================= --}}
       @foreach ($terms as $term)
<div class="grading-form grading-form-{{ $term }}"
     style="{{ $activeTerm === $term ? 'display:block;' : 'display:none;' }}">

    <h2 class="grading-h">Grading - {{ $term }}</h2>

    <div class="border-grading-form">
        <form action="{{ route('grading_view.addPercentageAndScores', ['class' => $class]) }}"
              method="POST" id="gradingForm-{{ $term }}">
            @csrf
            @method('PUT')
            

            <input type="hidden" name="periodic_term" value="{{ $term }}">

            <div class="table-grading-form-box">
                <div class="grading-setup">

                    {{-- QUIZZES --}}
                    <div class="grading-item">
                        <h4>Quizzes</h4>
                        <label>Percentage (%)</label>
                        <input type="number" step="0.01" name="quiz_percentage"
                               value="{{ $percentage[$term]->quiz_percentage ?? 0 }}">

                        <label>Total Score</label>
                        <input type="number" step="0.01" name="quiz_total_score"
                               value="{{ $percentage[$term]->quiz_total_score ?? 0 }}">
                    </div>

                    {{-- ATTENDANCE --}}
                    <div class="grading-item">
                        <h4>Attendance / Behavior</h4>
                        <label>Percentage (%)</label>
                        <input type="number" step="0.01" name="attendance_percentage"
                               value="{{ $percentage[$term]->attendance_percentage ?? 0 }}">

                        <label>Total Score</label>
                        <input type="number" step="0.01" name="attendance_total_score"
                               value="{{ $percentage[$term]->attendance_total_score ?? 0 }}">
                    </div>

                    {{-- ASSIGNMENTS --}}
                    <div class="grading-item">
                        <h4>Assignments / Participation / Project</h4>
                        <label>Percentage (%)</label>
                        <input type="number" name="assignment_percentage"
                               value="{{ $percentage[$term]->assignment_percentage ?? 0 }}">

                        <label>Total Score</label>
                        <input type="number" name="assignment_total_score"
                               value="{{ $percentage[$term]->assignment_total_score ?? 0 }}">
                    </div>

                    {{-- EXAM --}}
                    <div class="grading-item">
                        <h4>Exam</h4>
                        <label>Percentage (%)</label>
                        <input type="number" name="exam_percentage"
                               value="{{ $percentage[$term]->exam_percentage ?? 0 }}">

                        <label>Total Score</label>
                        <input type="number" name="exam_total_score"
                               value="{{ $percentage[$term]->exam_total_score ?? 0 }}">
                    </div>

                </div>

                <button type="submit" class="save-btn">
                    💾 Save Grading and Total Score
                </button>

                <button type="submit" name="save_all" value="1" class="save-btn">
                    💾 Save Default All
                </button>

                <p class="note">⚠ Save first before entering student scores.</p>
            </div>
        </form>
    </div>
</div>
@endforeach

@if(session('swal_error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Invalid Percentage',
        text: @json(session('swal_error')),
        confirmButtonColor: '#d33'
    });
</script>
@endif
@if(session('swal_error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Invalid Score Input',
        text: @json(session('swal_error')),
        confirmButtonColor: '#d33'
    });
</script>
@endif
        {{-- ================= STUDENT LIST ================= --}}
        <h2 class="student-list">Student List</h2>
        <div class="border-student-list">
            <form method="POST" action="{{ route('grading_view.addQuizAndScore', ['class' => $class->id]) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="periodic_term" id="student-periodic-term">
                <input type="text" placeholder="Search Student Name or ID..." name="search" class="search-student">

                <div class="table-student-form-list">
                    <div class="student-setup">
                        <table class="student-item" border="1" cellspacing="0" cellpadding="5">
                            <thead>
                                <tr>
                                    <th rowspan="2">Student Name</th>
                                    <th colspan="3">Quizzes</th>
                                    <th colspan="3">Attendance</th>
                                    <th colspan="3">Assignments</th>
                                    <th colspan="3">Exam</th>
                                    <th colspan="3">Grades</th>
                                </tr>
                                <tr>
                                    <th>Accumulated Score</th>
                                    <th>Transmuted Grade</th>
                                    <th>Weighted Grade</th>
                                    <th>Accumulated Score</th>
                                    <th>Transmuted Grade</th>
                                    <th>Weighted Grade</th>
                                    <th>Accumulated Score</th>
                                    <th>Transmuted Grade</th>
                                    <th>Weighted Grade</th>
                                    <th>Accumulated Score</th>
                                    <th>Transmuted Grade</th>
                                    <th>Weighted Grade</th>
                                    <th>Raw Grades</th>
                                </tr>
                            </thead>
                            <tbody id="student-scores-body">
                            @foreach($students as $student)
                                <tr data-student-id="{{ $student->studentID }}">
                                    <td>{{ $student->name }}</td>

                                    <td><input type="number" name="scores[{{ $student->studentID }}][quizzez]" class="input-box" value="0" min="0" step="0.01"></td>
                                    <td><div class="readonly-box quizzez-score">0.00</div></td>
                                    <td><div class="readonly-box quizzez-weighted">0.00</div></td>

                                    <td><input type="number" name="scores[{{ $student->studentID }}][attendance_behavior]" class="input-box" value="0" min="0" step="0.01"></td>
                                    <td><div class="readonly-box attendance_behavior-score">0.00</div></td>
                                    <td><div class="readonly-box attendance_behavior-weighted">0.00</div></td>

                                    <td><input type="number" name="scores[{{ $student->studentID }}][assignments]" class="input-box" value="0" min="0" step="0.01"></td>
                                    <td><div class="readonly-box assignments-score">0.00</div></td>
                                    <td><div class="readonly-box assignments-weighted">0.00</div></td>

                                    <td><input type="number" name="scores[{{ $student->studentID }}][exam]" class="input-box" value="0" min="0" step="0.01"></td>
                                    <td><div class="readonly-box exam-score">0.00</div></td>
                                    <td><div class="readonly-box exam-weighted">0.00</div></td>

                                    <td>
                                        <div class="readonly-box">
                                            <p class="raw-grade-display">0.00</p>
                                            <input type="hidden" name="scores[{{ $student->studentID }}][raw_grade]" class="raw-grade-input" value="0.00">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


                @if($showCalculateButton)
                    <div class="student-listBtn">
                        <button type="submit" name="submit">💾 Calculate and Update Scores</button>
                    </div>
                @else
                    <p class="waiting-dean-approval">⏳ Waiting for Dean's Approval</p>
                @endif

            </form>
        </div>

    </div>
    @endif
</div>

{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".grading-tab");
    const studentPeriodInput = document.getElementById("student-periodic-term");
    const studentListHeader = document.querySelector(".student-list");

    function calculateScores(row, data = null) {
        const fields = ["quizzez", "attendance_behavior", "assignments", "exam"];
        let totalGrade = 0;

        fields.forEach(field => {
            const input = row.querySelector(`input[name*='[${field}]']`);
            const transDiv = row.querySelector(`.${field}-score`);
            const weightedDiv = row.querySelector(`.${field}-weighted`);
            if (!input || !transDiv || !weightedDiv) return;

            let score = parseFloat(input.value) || 0;
            let trans = score;
            let weighted = 0;

            if (data && data[field + "_transmuted"] !== undefined) {
                trans = parseFloat(data[field + "_transmuted"]) || 5.00;
                weighted = parseFloat(data[field + "_weighted"]) || 0.50;
            } else {
                const term = studentPeriodInput.value;
                const percentageInput = document.querySelector(`#gradingForm-${term} input[name='${field.replace('_','')}_percentage']`);
                const totalScoreInput = document.querySelector(`#gradingForm-${term} input[name='${field.replace('_','')}_total_score']`);
                const percent = parseFloat(percentageInput?.value) || 0;
                const maxScore = parseFloat(totalScoreInput?.value) || 0;

                trans = maxScore > 0 ? score : 0;
                if (trans === 0) trans = 5.00;
                weighted = percent > 0 && maxScore > 0 ? (trans / maxScore) * percent : 0.50;
            }

            transDiv.textContent = trans.toFixed(2);
            weightedDiv.textContent = weighted.toFixed(2);
            totalGrade += weighted;
        });

        const totalDiv = row.querySelector("td:last-child .readonly-box p");
        if (totalDiv) totalDiv.textContent = totalGrade.toFixed(2);
    }

    function attachInputListeners() {
        document.querySelectorAll("#student-scores-body tr").forEach(row => {
            row.querySelectorAll("input.input-box").forEach(input => {
                input.addEventListener("input", () => calculateScores(row));
            });
        });
    }
    attachInputListeners();

    function loadScores(term) {
        fetch(`/grading/scores/{{ $class->id }}/${term}`)
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll("#student-scores-body tr").forEach(row => {
                    const studentId = row.dataset.studentId;
                    const scoreData = data[studentId] || {};

                    ["quizzez", "attendance_behavior", "assignments", "exam"].forEach(field => {
                        const input = row.querySelector(`input[name*='[${field}]']`);
                        if (input) input.value = scoreData[field] ?? 0;
                    });

                    calculateScores(row, scoreData);
                });
            })
            .catch(err => console.error("Error loading scores:", err));
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            document.querySelectorAll(".grading-form").forEach(form => form.style.display = "none");
            tabs.forEach(t => t.classList.remove("active"));

            this.classList.add("active");
            const term = this.textContent.trim();
            const form = document.querySelector(".grading-form-" + term);
            if (form) form.style.display = "block";

            studentPeriodInput.value = term;
            studentListHeader.textContent = `Student List (${term})`;

            loadScores(term);
        });
    });

    if (tabs.length > 0) tabs[0].click();

    window.saveAllDefaults = function(term) {
        @foreach ($terms as $t)
        (function(targetTerm) {
            const targetForm = document.querySelector("#gradingForm-" + targetTerm);
            if (targetForm) {
                const formData = new FormData(document.querySelector("#gradingForm-" + term));
                formData.set("periodic_term", targetTerm);

                fetch(targetForm.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                }).then(res => console.log("✅ Saved for " + targetTerm));
            }
        })("{{ $t }}");
        @endforeach
        alert("✅ Default grading setup saved for all terms!");
    }
});
</script>
@endsection
