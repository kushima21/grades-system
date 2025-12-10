<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\FinalGrade;
use App\Models\Percentage;
use App\Models\QuizzesAndScores;
use App\Models\Classes_Student;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\RawGrade;
use App\Models\FinalTransmutation;


class RegistrarController extends Controller
{
    public function index()
    {
        return view("registrar.registrar_dashboard");
    }
public function registrar_classes()
{
    $classes = Classes::orderBy('id', 'desc')->get();
    $instructors = User::where('role', 'LIKE', '%instructor%')->get();
    $classes_student = Classes_Student::all()->groupBy('classID');
    $finalGrades = DB::table('final_grade')->get();

    // Fetch logged in user's role as array (lowercase for safety)
    $userRoles = [strtolower(auth()->user()->role)];

    // Add total student count per class
    foreach ($classes as $class) {
        $class->totalStudents = Classes_Student::where('classID', $class->id)->count();
    }

    return view(
        'registrar.classes',
        compact('classes', 'instructors', 'classes_student', 'finalGrades', 'userRoles')
    );
}



public function searchInstructor(Request $request)
{
    $query = $request->input('query');

    $instructors = User::where('role', 'LIKE', '%instructor%')
        ->where('name', 'LIKE', $query . '%')
        ->select('id', 'name')
        ->limit(5)
        ->get();

    return response()->json($instructors);
}



public function CreateClass(Request $request)
{
    $request->validate([
        "course_no" => "required",
        // Remove this to avoid "required" error before we compute it manually:
        // "descriptive_title" => "required",
        "units" => "required",
        "instructor" => "required",
        "academic_period" => "required",
        "academic_year" => "required",
        "schedule" => "required",
        "department" => "required",
        "status" => "required",
        "added_by" => "required"
    ]);

    // Fetch descriptive title from DB based on selected course_no
    $course = Course::where('course_no', $request->course_no)->first();

    if (!$course) {
        return redirect()->back()->withInput()->with('error', "Course with code '{$request->course_no}' not found.");
    }

    // Validate the instructor
    $instructor_name = $request->instructor;
    $instructor = User::where('name', $instructor_name)->first();

    if (!$instructor) {
        return redirect()->back()->withInput()->with('error', "The instructor '{$instructor_name}' does not exist in the system.");
    }

    $class = new Classes();
    $class->course_no = $request->course_no;
    $class->descriptive_title = $course->descriptive_title; // fetched from DB
    $class->units = $request->units;
    $class->instructor = $request->instructor;
    $class->academic_period = $request->academic_period;
    $class->academic_year = $request->academic_year;
    $class->schedule = $request->schedule;
    $class->department = $request->department;
    $class->status = $request->status;
    $class->added_by = $request->added_by;

    if ($class->save()) {
        $user = Auth::user();

        // Store notification
        DB::table('notif_table')->insert([
            'notif_type'              => 'Class Added',
            'class_id'                => $class->id,
            'class_course_no'         => $class->course_no,
            'class_descriptive_title' => $class->descriptive_title,
            'department'              => $user->department ?? null,
            'added_by_id'             => $user->studentID,
            'added_by_name'           => $user->name,
            'target_by_id'            => $instructor->studentID ?? null,
            'target_by_name'          => $instructor->name ?? null,
            'status_from_added'       => 'unchecked',
            'status_from_target'      => 'unchecked',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect(route("registrar_classes"))->with("success", "Class Created Successfully");
    }

    return redirect(route("registrar_classes"))->withInput()->with("error", "Class Creation Failed");
}


public function EditClass(Request $request, Classes $class)
{
    $request->validate([
        "course_no" => "required", // changed from subject_code
        "descriptive_title" => "required",
        "units" => "required",
        "instructor" => "required",
        "academic_period" => "required",
        "academic_year" => "required",
        "schedule" => "required",
        "status" => "required",
    ]);

    $class->course_no = $request->course_no; // changed
    $class->descriptive_title = $request->descriptive_title;
    $class->units = $request->units;
    $class->instructor = $request->instructor;
    $class->academic_period = $request->academic_period;
    $class->academic_year = $request->academic_year;
    $class->schedule = $request->schedule;
    $class->status = $request->status;

    if ($class->save()) {
        $user = Auth::user();

        $instructor_name = $class->instructor;
        $instructor = User::where('name', $instructor_name)->first();

        if (!$instructor) {
            return redirect()->back()->with('error', "The instructor '{$instructor_name}' does not exist in the system.");
        }

        DB::table('notif_table')->insert([
            'notif_type'              => 'Class Edited',
            'class_id'                => $class->id,
            'class_course_no'         => $class->course_no, // changed
            'class_descriptive_title' => $class->descriptive_title,
            'department'              => $user->department ?? null,
            'added_by_id'             => $user->studentID,
            'added_by_name'           => $user->name,
            'target_by_id'            => $instructor->studentID ?? null,
            'target_by_name'          => $instructor->name ?? null,
            'status_from_added'       => 'unchecked',
            'status_from_target'      => 'unchecked',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return redirect(route("registrar_classes"))->with("success", "Class Edited Successfully");
    }

    return redirect(route("registrar_classes"))->with("error", "Class Edition Failed");
}


    public function DeleteClass(Classes $class)
    {
        try {
            $user = Auth::user();

            $instructor_name = $class->instructor; // this is a name like "Dave"
            $instructor = User::where('name', $instructor_name)->first();

            // Store notification
            DB::table('notif_table')->insert([
                'notif_type'      => 'Class Deleted',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null, // Optional if you store department
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'target_by_id'    => $instructor->studentID ?? null,
                'target_by_name'  => $instructor->name ?? null,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // Delete related records
            DB::table('classes_student')->where('classID', $class->id)->delete();
            DB::table('final_grade')->where('classID', $class->id)->delete();
            DB::table('percentage')->where('classID', $class->id)->delete();
            DB::table('quizzes_scores')->where('classID', $class->id)->delete();

            // Delete the class from the database
            $class->delete();

            return redirect()->route('registrar_classes')->with('success', 'Class and its related records deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('registrar_classes')->with('error', 'Failed to delete class. Please try again.');
        }
    }


public function show(Request $request, $class)
{
    $classes = Classes::where('id', $class)->first();

    if (!$classes) {
        return redirect()->route('instructor.my_class')
                         ->with('warning', 'The class you are trying to view no longer exists.');
    }

    // Count enrolled students
    $totalStudents = Classes_Student::where('classID', $class)->count();

    // Students not yet enrolled
    $enrolledStudentIds = Classes_Student::where('classID', $class)->pluck('studentID')->toArray();

    $students = User::where('role', 'student')
        ->whereNotIn('studentID', $enrolledStudentIds)
        ->get();

    // Enrolled students
    $classes_student = Classes_Student::where('classID', $class)->get();

    // Quizzes and percentages
    $quizzesandscores = QuizzesAndScores::where('classID', $class)->get();
    $percentage = Percentage::where('classID', $class)->get();

    // Final grades
    $finalGrades = DB::table('final_grade')
        ->where('classID', $class)
        ->get();

    // Check if dean_status is Confirmed for this class
    $deanStatusConfirmed = DB::table('final_grade')
        ->where('classID', $class)
        ->where('dean_status', 'Confirmed')
        ->exists();

    // Check if ALL final grades are Locked & Submitted
    $hasLockedAndSubmitted = $finalGrades->isNotEmpty() &&
        $finalGrades->every(function ($grade) {
            return $grade->status === 'Locked' && $grade->submit_status === 'Submitted';
        });

    // ===============================
    // FILTER STUDENTS BY DEPARTMENT
    // ===============================
    $user = Auth::user();
    $userRoles = explode(',', $user->role);

    if (in_array('dean', $userRoles)) {
        $userDepartment = $user->department;

        if (strtolower($userDepartment) === 'bachelor of science in education') {
            // Dean of BSEd can see both BEED and BSED students
            $filteredStudents = Classes_Student::where('classID', $class)
                ->whereIn('department', [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education'
                ])
                ->get();
        } else {
            // Other deans: only their own department
            $filteredStudents = Classes_Student::where('classID', $class)
                ->where('department', $userDepartment)
                ->get();
        }
    } else {
        // Non-deans: show all students
        $filteredStudents = Classes_Student::where('classID', $class)->get();
    }

    return view('registrar.classes_view', compact(
        'classes',
        'students',
        'classes_student',
        'quizzesandscores',
        'percentage',
        'finalGrades',
        'filteredStudents',
        'hasLockedAndSubmitted',
        'deanStatusConfirmed',
        'totalStudents' // 🔥 pass total student count
    ));
}





public function importCSV(Request $request, $class)
{
    // Fetch class model
    $class = Classes::findOrFail($class);

    // Validate file
    $request->validate([
        'students_csv' => 'required|mimes:csv,txt|max:2048'
    ]);

    // Read CSV file
    $file = $request->file('students_csv');
    $csvData = array_map('str_getcsv', file($file));

    // Remove CSV header row
    array_shift($csvData);

    $programToDepartment = [
        'BSBA' => 'Bachelor of Science in Business Administration',
        'BSBA-OM' => 'Bachelor of Science in Business Administration',
        'BSBA-FM' => 'Bachelor of Science in Business Administration',
        'BSCS' => 'Bachelor of Science in Computer Science',
        'BSSW' => 'Bachelor of Science in Social Work',
        'BAELS' => 'Bachelor of Arts in English Language Studies',
        'BEED'  => 'Bachelor of Elementary Education',
        'BSED-Math' => 'Bachelor of Secondary Education',
        'BSED-English' => 'Bachelor of Secondary Education',
        'BSED' => 'Bachelor of Secondary Education',
        'BSCRIM' => 'Bachelor of Science in Criminology',
    ];

    $students = [];
    $insertedStudentIDs = [];

    foreach ($csvData as $row) {
        if (count($row) < 5) continue; // Skip invalid rows

        $fullname = trim($row[1] . ", " . $row[2] . " " . $row[3]);
        $program = strtoupper(trim(explode('-', $row[6])[0]));

        $department = $programToDepartment[$program] ?? 'Unknown Department';

        $students[] = [
            'studentID'  => $row[4],
            'email'      => $row[5],
            'name'       => $fullname,
            'gender'     => null,
            'department' => $department,
            'classID'    => $class->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insertedStudentIDs[] = $row[4];
    }

    // Bulk insert students
    Classes_Student::insert($students);

    // Insert default quiz scores
    $periodicTerms = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];
    $quizScores = [];

    foreach ($insertedStudentIDs as $studentID) {
        foreach ($periodicTerms as $term) {
            $quizScores[] = [
                'classID'             => $class->id,
                'studentID'           => $studentID,
                'periodic_term'       => $term,
                'quizzez'             => 0,
                'attendance_behavior' => 0,
                'assignments'         => 0,
                'exam'                => 0,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
    }

    QuizzesAndScores::insert($quizScores);

    return back()->with('success', 'Students imported successfully.');
}



    public function addstudent(Request $request, Classes $class)
    {
        $request->validate([
            "student_id" => "required",
            "name" => "required",
            "gender" => "required",
            "email" => "required|email",
            "department" => "required",
        ]);

        // Create a new instance of Classes_Student and assign the values
        $classStudent = new Classes_Student();
        $classStudent->classID = $class->id;
        $classStudent->studentID = $request->student_id;
        $classStudent->name = $request->name;
        $classStudent->gender = $request->gender;
        $classStudent->email = $request->email;
        $classStudent->department = $request->department;

        // Array of periodic terms
        $periodicTerms = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

        // Save the instance of Classes_Student
        if ($classStudent->save()) {
            // Insert a row for each periodic term in quizzes_scores
            foreach ($periodicTerms as $term) {
                $quizzesandscores = new QuizzesAndScores();
                $quizzesandscores->classID = $class->id;
                $quizzesandscores->studentID = $request->student_id;
                $quizzesandscores->periodic_term = $term;
                $quizzesandscores->quizzez = 0;              // Default 0
                $quizzesandscores->attendance_behavior = 0;  // Default 0
                $quizzesandscores->assignments = 0;          // Default 0
                $quizzesandscores->exam = 0;                 // Default 0
                $quizzesandscores->save();
            }

            return redirect()->route("class.show", $class->id)->with("success", "Student added successfully.");
        }

        return redirect()->route("class.show", $class->id)->with("error", "Failed to add student. Please try again.");
    }


    public function removestudent($class, $student)
{
    $classStudent = Classes_Student::where('classID', $class)
        ->where('studentID', $student)
        ->first();

    $quizzesScores = QuizzesAndScores::where('classID', $class)
        ->where('studentID', $student)
        ->get();

    $finalGrade = FinalGrade::where('classID', $class)
        ->where('studentID', $student)
        ->first();

    if ($classStudent || $quizzesScores->isNotEmpty() || $finalGrade) {
        if ($classStudent) {
            $classStudent->delete();
        }
        if ($quizzesScores->isNotEmpty()) {
            foreach ($quizzesScores as $score) {
                $score->delete();
            }
        }
        if ($finalGrade) {
            $finalGrade->delete();
        }

        return redirect()->route("class.show", $class)
            ->with("success", "Student removed successfully.");
    }

    return redirect()->route("class.show", $class)
        ->with("error", "Student not found or already removed.");
}



public function addPercentageAndScores(Request $request, $class)
{
    $warnings = [];

    // Same inputs for all terms
    $inputData = [
        'quiz_percentage'       => $request->input("quiz_percentage") ?? 0,
        'quiz_total_score'      => $request->input("quiz_total_score") ?? 0,
        'attendance_percentage' => $request->input("attendance_percentage") ?? 0,
        'attendance_total_score'=> $request->input("attendance_total_score") ?? 0,
        'assignment_percentage' => $request->input("assignment_percentage") ?? 0,
        'assignment_total_score'=> $request->input("assignment_total_score") ?? 0,
        'exam_percentage'       => $request->input("exam_percentage") ?? 0,
        'exam_total_score'      => $request->input("exam_total_score") ?? 0,
    ];

    // check percentage total (para dili kaayohan og loop)
    $totalPercentage = $inputData['quiz_percentage'] + $inputData['attendance_percentage'] +
                       $inputData['assignment_percentage'] + $inputData['exam_percentage'];

    if ($totalPercentage != 100) {
        return redirect()->route("instructor.grading_view", $class)
            ->withErrors(["The total percentage must equal 100%."]);
    }

    // kung gi–click ang "Save Default All"
    if ($request->has('save_all')) {
        $terms = ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

        foreach ($terms as $term) {
            // warnings check per term
            foreach (['quiz', 'attendance', 'assignment', 'exam'] as $category) {
                $totalScore = $inputData["{$category}_total_score"];
                $scoreExists = DB::table('transmuted_grade')
                    ->where('score_bracket', $totalScore)
                    ->exists();

                if (!$scoreExists) {
                    $warnings[] = "⚠️ WARNING! The total score of $totalScore for " . ucfirst($category) .
                        " in $term does not exist in the database.";
                }
            }

            // save per term
            Percentage::updateOrCreate(
                ['classID' => $class, 'periodic_term' => $term],
                $inputData
            );
        }

        return redirect()->route("instructor.grading_view", $class)
            ->with('success', 'Default grading saved for all terms.')
            ->with('warnings', $warnings);
    }

    // else: save for one specific term only
    $term = $request->input('periodic_term');

    foreach (['quiz', 'attendance', 'assignment', 'exam'] as $category) {
        $totalScore = $inputData["{$category}_total_score"];
        $scoreExists = DB::table('transmuted_grade')
            ->where('score_bracket', $totalScore)
            ->exists();

        if (!$scoreExists) {
            $warnings[] = "⚠️ WARNING! The total score of $totalScore for " . ucfirst($category) .
                " in $term does not exist in the database.";
        }
    }

    Percentage::updateOrCreate(
        ['classID' => $class, 'periodic_term' => $term],
        $inputData
    );

   return redirect()->route('instructor.grading_view', [
    'id' => $class->id,
    'academic_period' => $class->academic_period
])->with('success', "Data saved successfully for $term.")
  ->with('warnings', $warnings);

}



// OPEN TAG: Add Quiz and Score
public function addQuizAndScore(Request $request, $class)
{
    $scores       = $request->input('scores');
    $periodicTerm = $request->input('periodic_term');

    // Get class details
    $classDetails = Classes::find($class);
    if (!$classDetails) {
        return redirect()->back()->with('error', 'Class not found.');
    }

    // Retrieve percentage info
    $percentage = Percentage::where('classID', $class)
        ->where('periodic_term', $periodicTerm)
        ->first();
    if (!$percentage) {
        return redirect()->back()->with('error', 'Percentage data not found for this class.');
    }

    foreach ($scores as $studentId => $fields) {

        // Get student info
        $classStudent = Classes_Student::where('classID', $class)
            ->where('studentID', $studentId)
            ->first();
        if (!$classStudent) continue;

        $studentName = $classStudent->name ?? "Student ID $studentId";

        // VALIDATIONS
        if (($fields['quizzez'] ?? 0) > $percentage->quiz_total_score) {
            return redirect()->back()->with('error', "Quiz score for {$studentName} exceeds total.");
        }
        if (($fields['attendance_behavior'] ?? 0) > $percentage->attendance_total_score) {
            return redirect()->back()->with('error', "Attendance score for {$studentName} exceeds total.");
        }
        if (($fields['assignments'] ?? 0) > $percentage->assignment_total_score) {
            return redirect()->back()->with('error', "Assignment score for {$studentName} exceeds total.");
        }
        if (($fields['exam'] ?? 0) > $percentage->exam_total_score) {
            return redirect()->back()->with('error', "Exam score for {$studentName} exceeds total.");
        }

        // SAVE QUIZZES AND SCORES
        QuizzesAndScores::updateOrCreate(
            [
                'classID'       => $class,
                'studentID'     => $studentId,
                'periodic_term' => $periodicTerm,
            ],
            [
                'quizzez'             => $fields['quizzez'] ?? 0,
                'attendance_behavior' => $fields['attendance_behavior'] ?? 0,
                'assignments'         => $fields['assignments'] ?? 0,
                'exam'                => $fields['exam'] ?? 0,
                'updated_at'          => now(),
            ]
        );

        // COMPUTE TRANSMUTED
        $quizTrans = $this->getTransmutedGrade($fields['quizzez'] ?? 0, $percentage->quiz_total_score);
        $attTrans  = $this->getTransmutedGrade($fields['attendance_behavior'] ?? 0, $percentage->attendance_total_score);
        $assTrans  = $this->getTransmutedGrade($fields['assignments'] ?? 0, $percentage->assignment_total_score);
        $examTrans = $this->getTransmutedGrade($fields['exam'] ?? 0, $percentage->exam_total_score);

        $quizWeighted = $this->getWeightedGrade($quizTrans, $percentage->quiz_percentage);
        $attWeighted  = $this->getWeightedGrade($attTrans, $percentage->attendance_percentage);
        $assWeighted  = $this->getWeightedGrade($assTrans, $percentage->assignment_percentage);
        $examWeighted = $this->getWeightedGrade($examTrans, $percentage->exam_percentage);

        $finalTransmutedGrade = $quizWeighted + $attWeighted + $assWeighted + $examWeighted;

        // MAP COLUMN FOR RAW VALUE
        $columnToUpdate = match (strtolower($periodicTerm)) {
            'prelim'        => 'prelim',
            'midterm'       => 'midterm_raw',
            'semi-finals'   => 'semi_finals_raw',
            'finals'        => 'final_raw',
            default         => null,
        };
        if (!$columnToUpdate) continue;

        // GET OR CREATE RAW GRADE
        $raw = RawGrade::firstOrCreate(
            ['studentID' => $studentId, 'classID' => $class],
            [
                'course_no'         => $classDetails->course_no,
                'descriptive_title' => $classDetails->descriptive_title,
                'instructor'        => $classDetails->instructor,
                'academic_period'   => $classDetails->academic_period,
                'schedule'          => $classDetails->schedule,
                'name'              => $classStudent->name,
                'gender'            => $classStudent->gender,
                'email'             => $classStudent->email,
                'department'        => $classStudent->department,
            ]
        );

        // UPDATE CURRENT TERM RAW VALUE
        $raw->$columnToUpdate = $finalTransmutedGrade;

        // --- RECALCULATE DEPENDENT COMPUTED GRADES ---
        $prelimRaw     = $raw->prelim ?? 0;
        $midtermRaw    = $raw->midterm_raw ?? 0;
        $semiRaw       = $raw->semi_finals_raw ?? 0;
        $finalRaw      = $raw->final_raw ?? 0;

        // Midterm = 0.33 prelim + 0.67 midterm_raw
        $raw->midterm = ($prelimRaw * 0.33) + ($midtermRaw * 0.67);

        // Semi-Finals = 0.33 midterm + 0.67 semi_finals_raw
        $raw->semi_finals = ($raw->midterm * 0.33) + ($semiRaw * 0.67);

        // Final = 0.33 semi-finals + 0.67 final_raw
        $raw->final = ($raw->semi_finals * 0.33) + ($finalRaw * 0.67);

        $raw->updated_at = now();
        $raw->save();
    }

    return redirect()->back()->with('success', 'Scores and raw grades saved successfully.');
}


// CLOSE TAG: Add Quiz and Score

/*student Grades view*/
public function studentGradesView($id, $academic_period, Request $request)
{
    $grades = \App\Models\RawGrade::where('classID', $id)
        ->where('academic_period', $academic_period)
        ->get();

    $class = \App\Models\Classes::find($id);

    // Get Department Status from final_grade
    $departmentStatus = \App\Models\FinalGrade::select(
        'department',
        \DB::raw("MAX(status) as status"),
        \DB::raw("MAX(submit_status) as submit_status"),
        \DB::raw("MAX(dean_status) as dean_status"),
        \DB::raw("MAX(registrar_status) as registrar_status")
    )
    ->where('classID', $id)
    ->groupBy('department')
    ->get()
    ->keyBy('department');

    // 🔍 Check if there are still raw_grades NOT saved in final_grade OR not locked/submitted
    $gradesToInitializeExist = \App\Models\RawGrade::where('classID', $id)
        ->where('academic_period', $academic_period)
        ->where(function($query) use ($id) {
            $query->whereNotIn('studentID', function($sub) use ($id) {
                $sub->select('studentID')
                    ->from('final_grade')
                    ->where('classID', $id)
                    ->where(function($q) {
                        $q->where('status', 'Locked')
                          ->orWhere('submit_status', 'Submitted')
                          ->orWhere('dean_status', 'Approved'); // kung gusto nimo gi-consider nga approved ang dean
                    });
            })
            ->orWhereNull('remarks'); // optional: para ma-check pud ang raw grades nga wala remarks
        })
        ->exists();

    return view('instructor.student&grades_view', compact(
        'grades',
        'class',
        'academic_period',
        'departmentStatus',
        'gradesToInitializeExist'
    ));
}



/* close */
// Show Grading View
public function showGrading($id, $academic_period)
{
    $class = Classes::findOrFail($id);

    $terms = ($academic_period === 'Summer')
        ? ['Prelim', 'Finals']
        : ['Prelim', 'Midterm', 'Semi-Finals', 'Finals'];

    $percentage = [];
    foreach ($terms as $term) {
        $percentage[$term] = Percentage::where('classID', $class->id)
            ->where('periodic_term', $term)
            ->first();
    }

    // Get all enrolled students in this class
    $students = Classes_Student::where('classID', $class->id)->get();

    $quizzesScores = QuizzesAndScores::where('classID', $class->id)->get()->keyBy('studentID');

    // Fetch final grades keyed by studentID
    $finalGrades = FinalGrade::where('classID', $class->id)->get()->keyBy('studentID');

    $rawGrades = RawGrade::where('classID', $class->id)->get()->keyBy('studentID');

    // =========================
    // Determine if "Calculate" button should show
    // =========================
    $showCalculateButton = false;

    foreach ($students as $student) {
        $finalGrade = $finalGrades[$student->studentID] ?? null;

        $status = trim($finalGrade->status ?? '');
        $submit_status = trim($finalGrade->submit_status ?? '');

        // Only skip students who are Locked + Submitted
        if (!$finalGrade || $status !== 'Locked' || $submit_status !== 'Submitted') {
            $showCalculateButton = true;
            break; // at least one student needs grading
        }
    }

    return view('instructor.grading_view', compact(
        'class', 'percentage', 'students', 'quizzesScores', 'finalGrades', 'rawGrades',
        'academic_period', 'terms', 'showCalculateButton'
    ));
}












public function getStudentScores($classId, $term)
{
    $students   = Classes_Student::where('classID', $classId)->get();
    $percentage = Percentage::where('classID', $classId)
        ->where('periodic_term', $term)
        ->first();

    $scores = [];
    foreach ($students as $student) {
        $record = QuizzesAndScores::where('classID', $classId)
            ->where('studentID', $student->studentID)
            ->where('periodic_term', $term)
            ->first();

        $quizScore = $record->quizzez ?? 0;
        $attScore  = $record->attendance_behavior ?? 0;
        $assScore  = $record->assignments ?? 0;
        $examScore = $record->exam ?? 0;

        // transmuted & weighted helpers
        $quizTrans = $this->getTransmutedGrade($quizScore, $percentage->quiz_total_score ?? 0);
        $attTrans  = $this->getTransmutedGrade($attScore, $percentage->attendance_total_score ?? 0);
        $assTrans  = $this->getTransmutedGrade($assScore, $percentage->assignment_total_score ?? 0);
        $examTrans = $this->getTransmutedGrade($examScore, $percentage->exam_total_score ?? 0);

    $scores[$student->studentID] = [
    'quizzez'                   => $quizScore,
    'quizzez_transmuted'        => $quizTrans ?: 5.00,
    'quizzez_weighted'          => $quizTrans > 0 ? $this->getWeightedGrade($quizTrans, $percentage->quiz_percentage ?? 0) : 0.50,

    'attendance_behavior'       => $attScore,
    'attendance_behavior_transmuted' => $attTrans ?: 5.00,
    'attendance_behavior_weighted'   => $attTrans > 0 ? $this->getWeightedGrade($attTrans, $percentage->attendance_percentage ?? 0) : 0.50,

    'assignments'               => $assScore,
    'assignments_transmuted'    => $assTrans ?: 5.00,
    'assignments_weighted'      => $assTrans > 0 ? $this->getWeightedGrade($assTrans, $percentage->assignment_percentage ?? 0) : 0.50,

    'exam'                      => $examScore,
    'exam_transmuted'           => $examTrans ?: 5.00,
    'exam_weighted'             => $examTrans > 0 ? $this->getWeightedGrade($examTrans, $percentage->exam_percentage ?? 0) : 0.50,
];
    }

    return response()->json($scores);
}

// helper functions
private function getTransmutedGrade($fieldScore, $totalScore)
{
    if ($totalScore > 0 && $fieldScore > 0) {
        $entry = DB::table('transmuted_grade')
            ->where('score_bracket', $totalScore)
            ->where('score', '<=', $fieldScore)
            ->orderBy('score', 'desc')
            ->first();
        $trans = $entry?->transmuted_grade ?? 0;
        return $trans > 0 ? $trans : 5.00; // default 5.00 if transmuted = 0
    }
    return 5.00; // default 5.00 if no score
}

private function getWeightedGrade($transmutedGrade, $percentage)
{
    if ($transmutedGrade == 0) return 0.50; // special rule
    return (!is_null($transmutedGrade) && $percentage > 0)
        ? ($transmutedGrade * $percentage) / 100
        : 0;
}


public function initializeGrades(Request $request)
{
    $grades = $request->grades;

    if (empty($grades) || !is_array($grades)) {
        return back()->with('error', 'No students yet, you can\'t initialize.');
    }

    // Get all transmutations sorted ascending by 'grades'
    $transmutations = FinalTransmutation::orderBy('grades', 'asc')->get();

    foreach ($grades as $grade) {

        // Make sure $grade is array, not string
        if (!is_array($grade)) continue;

        $classInfo   = Classes::find($grade['classID']);
        $studentInfo = Classes_Student::where('studentID', $grade['studentID'])->first();

        $finalGrade = floatval($grade['final'] ?? 0); // cast to float for numeric comparison

        // Step 1: Try to match using 'grades'
        $matched = $transmutations->filter(fn($t) => $finalGrade >= floatval($t->grades))->last();

        // Step 2: If no match on 'grades', try 'transmutation'
        if (!$matched) {
            $matched = $transmutations->filter(fn($t) => $finalGrade >= floatval($t->transmutation))->last();
        }

        // Default remarks if still not matched
        $remarks = $matched ? $matched->remarks : 'Failed';

        // Update raw_grades table
        DB::table('raw_grades')
            ->where('classID', $grade['classID'])
            ->where('studentID', $grade['studentID'])
            ->update([
                'remarks' => $remarks
            ]);

        // Insert or update final_grade table
       // Check if a final_grade row already exists
$existing = DB::table('final_grade')
    ->where('classID', $grade['classID'])
    ->where('studentID', $grade['studentID'])
    ->first();

DB::table('final_grade')->updateOrInsert(
    [
        'classID'   => $grade['classID'],
        'studentID' => $grade['studentID']
    ],
    [
        'course_no'         => optional($classInfo)->course_no,
        'descriptive_title' => optional($classInfo)->descriptive_title,
        'instructor'        => optional($classInfo)->instructor,
        'academic_period'   => optional($classInfo)->academic_period,
        'schedule'          => optional($classInfo)->schedule,

        'name'       => optional($studentInfo)->name,
        'gender'     => optional($studentInfo)->gender,
        'email'      => optional($studentInfo)->email,
        'department' => optional($studentInfo)->department,

        'prelim'      => $grade['prelim'] ?? null,
        'midterm'     => $grade['midterm'] ?? null,
        'semi_finals' => $grade['semi_finals'] ?? null,
        'final'       => $grade['final'] ?? null,

        'remarks'     => $remarks,
        'status'      => $existing->status ?? '', // ← preserve existing status
        'updated_at'  => now(),
        'created_at'  => $existing ? null : now(),
    ]
);

    }

    return back()->with('success', 'Grades have been initialized successfully!');
}




public function showClassGrades($classID)
{
    // Fetch class info
    $class = Classes::findOrFail($classID);

    // Fetch all grades for this class
    $grades = DB::table('final_grade')
        ->where('classID', $classID)
        ->get();

    // Calculate department status (locked + submitted)
    $departmentStatus = DB::table('final_grade')
        ->where('classID', $classID)
        ->select(
            DB::raw('TRIM(department) as department'),
            DB::raw('MAX(CASE WHEN TRIM(status) = "Locked" THEN 1 ELSE 0 END) as locked'),
            DB::raw('COUNT(CASE WHEN LOWER(TRIM(submit_status)) LIKE "submitted%" THEN 1 END) as submitted_count')
        )
        ->groupBy('department')
        ->get()
        ->mapWithKeys(function($row) {
            return [
                $row->department => (object)[
                    'status' => $row->locked ? 'Locked' : 'Not Yet Locked',
                    'submit_status' => $row->submitted_count > 0 ? 'Submitted' : 'Not Submitted Yet',
                    'dean_status' => 'Pending',        // optional default
                    'registrar_status' => 'Pending'    // optional default
                ]
            ];
        });

    // Group grades by department
    $studentsByDept = $grades->groupBy('department');

    // Return to registrar view
    return view('registrar.classes_view', [
        'class' => $class,
        'grades' => $grades,
        'studentsByDept' => $studentsByDept,
        'departmentStatus' => $departmentStatus
    ]);
}


public function lockInGrades(Request $request)
{
    $grades = $request->grades;
    if (empty($grades)) {
        return back()->with('error', 'No students yet, you can\'t lock.');
    }

    $classID = $request->classID;
    $department = $request->department;

    $classInfo = Classes::find($classID);

    foreach ($grades as $g) {
        $studentID = $g['studentID'];
        $studentInfo = Classes_Student::where('studentID', $studentID)->first();

        DB::table('final_grade')->updateOrInsert(
            [
                'classID' => $classID,
                'studentID' => $studentID
            ],
            [
                'course_no' => optional($classInfo)->course_no,
                'descriptive_title' => optional($classInfo)->descriptive_title,
                'instructor' => optional($classInfo)->instructor,
                'academic_period' => optional($classInfo)->academic_period,
                'schedule' => optional($classInfo)->schedule,

                'name' => optional($studentInfo)->name,
                'gender' => optional($studentInfo)->gender,
                'email' => optional($studentInfo)->email,
                'department' => optional($studentInfo)->department,

                'prelim' => $g['prelim'],
                'midterm' => $g['midterm'],
                'semi_finals' => $g['semi_finals'],
                'final' => $g['final'],
                'remarks' => $g['remarks'],

                'status' => 'Locked',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    return back()->with('success', 'Final grades for ' . $department . ' have been locked successfully!');
}





    public function UnlockGrades(Request $request)
    {
        $department = $request->input('department');
        $classID = $request->input('classID'); // 🔥 Include classID

        if (!$department || !$classID) {
            return back()->with('error', 'Invalid request. No department or class selected.');
        }

        // Unlock grades only for the specified department and class
        DB::table('final_grade')
            ->where('department', $department)
            ->where('classID', $classID) // 🔥 Ensure only this class is affected
            ->update(['status' => null]);

        Classes::where('id', $classID)->update(['status' => 'Active']);

        return back()->with('success', "Final grades have been unlocked!");
    }

public function submitToDean(Request $request)
{
    $request->validate([
        'classID' => 'required',
        'department' => 'required',
    ]);

    $classID = $request->classID;
    $department = $request->department;

    // Update final_grade table submission status
    DB::table('final_grade')
        ->where('classID', $classID)
        ->where('department', $department)
        ->update([
            'submit_status' => 'Submitted',
            'updated_at' => now(),
        ]);

    // Optional Class Status Message
    Classes::where('id', $classID)->update([
        'status' => "Pending Dean Approval ($department)",
        'updated_at' => now(),
    ]);

    // Notify Dean
    $class = Classes::find($classID);
    $submitter = Auth::user();
    $dean = User::where('role', 'dean')->first(); // adjust to your database

    DB::table('notif_table')->insert([
        'notif_type' => "Submitted to Dean",
        'class_id' => $classID,
        'department' => $department,
        'class_course_no' => $class->course_no,
        'class_descriptive_title' => $class->descriptive_title,
        'added_by_id' => $submitter->studentID,
        'added_by_name' => $submitter->name,
        'target_by_id' => $dean->studentID ?? null,
        'target_by_name' => $dean->name ?? null,
        'status_from_added' => 'unchecked',
        'status_from_target' => 'unchecked',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
return redirect(url()->previous() . '#' . $department)
    ->with('success', "Grades were submitted to Dean successfully!");
}

public function deanDecision(Request $request)
{
    $request->validate([
        'dean_status' => 'required',
        'department' => 'required',
        'classID' => 'required',
    ]);

    $department = $request->department;
    $classID = $request->classID;
    $deanStatus = $request->dean_status;
    $remarks = $request->dean_remarks ?? '';

    // Update final_grade table
    DB::table('final_grade')
        ->where('classID', $classID)
        ->where('department', $department)
        ->update([
            'dean_status' => $deanStatus,
            'dean_comment' => ($deanStatus === 'Returned') ? $remarks : '',
            'updated_at' => now(),
        ]);

    // Update class status message
    $class = Classes::find($classID);
    $statusMessage = ($deanStatus === 'Confirmed')
        ? 'Dean approved the grades. Ready to submit to Registrar'
        : 'Dean returned the grades. Waiting for instructor action';

    Classes::where('id', $classID)->update(['status' => $statusMessage]);

    // Notify Instructor
    $instructor = User::where('name', $class->instructor)->first();
    $dean = Auth::user();

    DB::table('notif_table')->insert([
        'notif_type' => "Dean's decision: $deanStatus",
        'class_id' => $classID,
        'class_course_no' => $class->course_no,
        'class_descriptive_title' => $class->descriptive_title,
        'department' => $department,
        'added_by_id' => $dean->studentID,
        'added_by_name' => $dean->name,
        'target_by_id' => $instructor->studentID ?? null,
        'target_by_name' => $instructor->name ?? null,
        'status_from_added' => 'unchecked',
        'status_from_target' => 'unchecked',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with('success', "Decision ($deanStatus) has been submitted successfully!");
}


public function submitToRegistrar(Request $request)
{
    $department = $request->input('department');
    $classID = $request->input('classID'); 

    if (!$department || !$classID) {
        return back()->with('error', 'Invalid request. No department or class selected.');
    }

    // Update submit_status to 'Submitted' for locked grades in the selected department and class
    DB::table('final_grade')
        ->where('department', $department)
        ->where('classID', $classID)
        ->where('status', 'Locked')
        ->update([
            'registrar_status' => 'Pending',
            'updated_at' => now(),
        ]);

    Classes::where('id', $classID)
        ->update(['status' => 'Grades has been submitted to the registrar, Waiting for approval']);

    $user = Auth::user();
    $class = Classes::find($classID);
    $users = User::all();

    $registrar = $users->first(function ($user) {
        $roles = explode(',', $user->role);
        return in_array('registrar', array_map('trim', $roles));
    });

    $instructor = $users->firstWhere('name', $class->instructor);

    $baseNotif = [
        'notif_type'      => 'Class grades submitted to the Registrar',
        'class_id'        => $class->id,
        'class_course_no' => $class->course_no,
        'class_descriptive_title' => $class->descriptive_title,
        'department'      => $user->department ?? null,
        'added_by_id'     => $user->studentID,
        'added_by_name'   => $user->name,
        'status_from_added'    => 'unchecked',
        'status_from_target'    => 'unchecked',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    DB::table('notif_table')->insert(array_merge($baseNotif, [
        'target_by_id' => $registrar->studentID ?? null,
        'target_by_name' => $registrar->name ?? null,
    ]));

    DB::table('notif_table')->insert(array_merge($baseNotif, [
        'target_by_id' => $instructor->studentID ?? null,
        'target_by_name' => $instructor->name ?? null,
    ]));

    return redirect()->route('registrar_classes')
        ->with('success', "Grades for $department have been submitted to the Registrar!");
}

    public function SubmitGradesRegistrar(Request $request)
    {
        $department = $request->input('department');
        $classID = $request->input('classID'); // 🔥 Include classID

        if (!$department || !$classID) {
            return back()->with('error', 'Invalid request. No department or class selected.');
        }

        // Update submit_status to 'Submitted' for locked grades in the selected department and class
        DB::table('final_grade')
            ->where('department', $department)
            ->where('classID', $classID) // 🔥 Ensure only this class is affected
            ->where('status', 'Locked')
            ->update([
                'registrar_status' => 'Pending',
                'updated_at' => now(),
            ]);


        Classes::where('id', $classID)->update(['status' => 'Grades has been submitted to the registrar, Waiting for approval']);

        $user = Auth::user();
        $class = Classes::find($classID);
        $users = User::all();

        // Get Registrar
        $registrar = $users->first(function ($user) {
            $roles = explode(',', $user->role); // assuming roles are comma-separated
            return in_array('registrar', array_map('trim', $roles));
        });

        // Get Instructor from class (match by name)
        $instructor = $users->firstWhere('name', $class->instructor);

        // Shared notification content
        $baseNotif = [
            'notif_type'      => 'Class grades submitted to the Registrar',
            'class_id'        => $class->id,
            'class_course_no' => $class->course_no,
            'class_descriptive_title' => $class->descriptive_title,
            'department'      => $user->department ?? null,
            'added_by_id'     => $user->studentID,
            'added_by_name'   => $user->name,
            'status_from_added'    => 'unchecked',
            'status_from_target'    => 'unchecked',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 🔔 Notify Registrar
        DB::table('notif_table')->insert(array_merge($baseNotif, [
            'target_by_id' => $registrar->studentID ?? null,
            'target_by_name' => $registrar->name ?? null,
        ]));

        // 🔔 Notify Instructor
        DB::table('notif_table')->insert(array_merge($baseNotif, [
            'target_by_id' => $instructor->studentID ?? null,
            'target_by_name' => $instructor->name ?? null,
        ]));


        return redirect()->route('registrar_classes')->with('success', "Grades for $department have been submitted to the Registrar!");
    }


    public function submitDecision(Request $request)
    {
        // Validate input
        $request->validate([
            'dean_status' => 'required',
            'classID' => 'required',
            'department' => 'required', // 🔥 Ensure department is required
            'dean_comment' => 'nullable|string'
        ]);

        // Build update data
        $updateData = [
            'dean_status' => $request->dean_status,
            'dean_comment' => $request->dean_comment,
            'updated_at' => now()
        ];

        // ✅ If "Returned", also update submit_status & class status
        if ($request->dean_status == 'Returned') {
            $updateData['submit_status'] = 'Returned';

            $user = Auth::user();

            $class = Classes::find($request->classID);

            // Get DEAN of the same department
            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Get INSTRUCTOR by matching class instructor name
            $instructor = User::where('name', $class->instructor)->first();

            // Shared notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been rejected by the dean of ' . $request->department . ' please review them.',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify DEAN
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify INSTRUCTOR
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));

            // 🔥 Update class status to "Rejected"
            Classes::where('id', $request->classID)->update(['status' => 'Grades returned by the dean of ' . $request->department . ' Please review them.']);
        }

        // ✅ If "Confirmed", update submit_status & class status
        if ($request->dean_status == 'Confirmed') {
            $updateData['submit_status'] = 'Submitted';


            $user = Auth::user();

            $class = Classes::find($request->classID);

            $instructor_name = $class->instructor; // this is a name like "Dave"
            $instructor = User::where('name', $instructor_name)->first();

            // Store notification
            DB::table('notif_table')->insert([
                'notif_type'      => 'Class grades has been approved by the dean of ' . $request->department . ' department ',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null, // Optional if you store department
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'target_by_id'    => $instructor->studentID ?? null,
                'target_by_name'  => $instructor->name ?? null,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔥 Update class status to "Approved"
            Classes::where('id', $request->classID)->update(['status' => 'Dean approved the submitted grade']);
        }


        // ✅ Update only records matching classID and department
        DB::table('final_grade')
            ->where('classID', $request->classID)
            ->where('department', $request->department)
            ->update($updateData);

        return back()->with('success', 'Dean’s decision has been submitted successfully!');
    }


    public function submitDecisionRegistrar(Request $request)
    {
        // Validate input
        $request->validate([
            'registrar_status' => 'required|string|in:Approved,Rejected',
            'classID' => 'required|integer',
            'department' => 'required|string', // 🔥 Ensure department is required
            'registrar_comment' => 'nullable|string'
        ]);

        // Build update data
        $updateData = [
            'registrar_status' => $request->registrar_status,
            'registrar_comment' => $request->registrar_status === 'Rejected' ? $request->registrar_comment : null,
            'updated_at' => now()
        ];

        // ✅ If "Rejected", also update submit_status & class status
        if ($request->registrar_status == 'Rejected') {
            $updateData['registrar_status'] = 'Rejected';
            $updateData['dean_status'] = 'Returned';

            $user = Auth::user();

            $class = Classes::find($request->classID);

            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            // Find the Dean
            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is stored as comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Find the Instructor by name (from class model)
            $instructor = User::where('name', $class->instructor)->first();

            // Common notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been rejected by the registrar. Please review them.',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'   => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify Dean
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify Instructor
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));


            // 🔥 Update class status to "Rejected"
            Classes::where('id', $request->classID)->update(['status' => 'Grades returned by the registrar, please review them.']);

        }

        // ✅ If "Approved", update submit_status & class status
        if ($request->registrar_status == 'Approved') {
            $updateData['registrar_status'] = 'Approved'; // Indicating final step before submission

            if (empty($request->grades)) {
                return back()->with('error', 'No students selected, you can\'t lock.');
            }

            $selectedDepartment = $request->department;
            $classIDs = [];

            foreach ($request->grades as $grade) {
                // Get student info for each department
                $studentInfo = Classes_Student::where('studentID', $grade['studentID'])
                    ->where('department', $selectedDepartment)
                    ->first();

                if ($studentInfo) {
                    $classInfo = Classes::find($grade['classID']);
                    $courseNo = optional($classInfo)->course_no;
                    $descriptiveTitle = optional($classInfo)->descriptive_title;
                    $units = optional($classInfo)->units;
                    $schedule = optional($classInfo)->schedule;
                    $instructor = optional($classInfo)->instructor;
                    $academicYear = optional($classInfo)->academic_year;
                    $academicPeriod = optional($classInfo)->academic_period;
                    $addedby = optional($classInfo)->added_by;

                    // Handle quizzes and scores for the student
                    $quizzesScores = DB::table('quizzes_scores')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->get();

                    foreach ($quizzesScores as $score) {
                        $percentageData = DB::table('percentage')
                            ->where('classID', $score->classID)
                            ->first();

                        DB::table('archived_quizzesandscores')->insert([
                            'classID' => $score->classID,
                            'course_no' => $courseNo,
                            'descriptive_title' => $descriptiveTitle,
                            'units' => $units,
                            'instructor' => $instructor,
                            'studentID' => $score->studentID,
                            'periodic_term' => $score->periodic_term,
                            'quiz_percentage' => $percentageData->quiz_percentage ?? null,
                            'quiz_total_score' => $percentageData->quiz_total_score ?? null,
                            'quizzez' => $score->quizzez,
                            'attendance_percentage' => $percentageData->attendance_percentage ?? null,
                            'attendance_total_score' => $percentageData->attendance_total_score ?? null,
                            'attendance_behavior' => $score->attendance_behavior,
                            'assignment_percentage' => $percentageData->assignment_percentage ?? null,
                            'assignment_total_score' => $percentageData->assignment_total_score ?? null,
                            'assignments' => $score->assignments,
                            'exam_percentage' => $percentageData->exam_percentage ?? null,
                            'exam_total_score' => $percentageData->exam_total_score ?? null,
                            'exam' => $score->exam,
                            'academic_period' => $academicPeriod,
                            'academic_year' => $academicYear,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Remove the student's quizzes and scores after transferring
                    DB::table('quizzes_scores')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->delete();

                    // Insert into final archived grades
                    DB::table('archived_final_grades')->insert([
                        'classID' => $grade['classID'],
                        'studentID' => $grade['studentID'],
                        'course_no' => $courseNo,
                        'descriptive_title' => $descriptiveTitle,
                        'units' => $units,
                        'schedule' => $schedule,
                        'instructor' => $instructor,
                        'academic_year' => $academicYear,
                        'academic_period' => $academicPeriod,
                        'name' => $studentInfo->name,
                        'gender' => $studentInfo->gender,
                        'email' => $studentInfo->email,
                        'department' => $selectedDepartment,
                        'prelim' => $grade['prelim'],
                        'midterm' => $grade['midterm'], 
                        'semi_finals' => $grade['semi_finals'],
                        'final' => $grade['final'],
                        'remarks' => $grade['remarks'],
                        'status' => 'Approved',
                        'added_by' => $addedby,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]);

                    // Insert into final archived grades
                    DB::table('grade_logs')->insert([
                        'classID' => $grade['classID'],
                        'studentID' => $grade['studentID'],
                        'course_no' => $courseNo,
                        'descriptive_title' => $descriptiveTitle,
                        'units' => $units,
                        'schedule' => $schedule,
                        'instructor' => $instructor,
                        'academic_year' => $academicYear,
                        'academic_period' => $academicPeriod,
                        'name' => $studentInfo->name,
                        'gender' => $studentInfo->gender,
                        'email' => $studentInfo->email,
                        'department' => $selectedDepartment,
                        'prelim' => $grade['prelim'],
                        'midterm' => $grade['midterm'],
                        'semi_finals' => $grade['semi_finals'],
                        'final' => $grade['final'],
                        'remarks' => $grade['remarks'],
                        'status' => 'Approved',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]);

                    // ✅ Remove student from classes_student
                    Classes_Student::where('studentID', $grade['studentID'])
                        ->where('classID', $grade['classID'])
                        ->delete();

                    // ✅ Remove student from final_grades after locking
                    DB::table('final_grade')
                        ->where('classID', $grade['classID'])
                        ->where('studentID', $grade['studentID'])
                        ->delete();

                    $classIDs[] = $grade['classID'];
                }
            }

            $user = Auth::user();

            $class = Classes::find($request->classID);

            // Get DEAN of the same department
            if (stripos($request->department, 'education') !== false) {
                $users = User::whereRaw('LOWER(department) LIKE ?', ['%education%'])->get();
            } else {
                $users = User::where('department', $request->department)->get();
            }

            $dean = $users->first(function ($user) {
                $roles = explode(',', $user->role); // assuming role is comma-separated
                return in_array('dean', array_map('trim', $roles));
            });

            // Get INSTRUCTOR by matching class instructor name
            $instructor = User::where('name', $class->instructor)->first();

            // Shared notification data
            $baseNotif = [
                'notif_type'      => 'Class grades have been approved by the registrar, please check your archive',
                'class_id'        => $class->id,
                'class_course_no' => $class->course_no,
                'class_descriptive_title' => $class->descriptive_title,
                'department'      => $user->department ?? null,
                'added_by_id'     => $user->studentID,
                'added_by_name'   => $user->name,
                'status_from_added'    => 'unchecked',
                'status_from_target'    => 'unchecked',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔔 Notify DEAN
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $dean->studentID ?? null,
                'target_by_name' => $dean->name ?? null,
            ]));

            // 🔔 Notify INSTRUCTOR
            DB::table('notif_table')->insert(array_merge($baseNotif, [
                'target_by_id' => $instructor->studentID ?? null,
                'target_by_name' => $instructor->name ?? null,
            ]));



            // ✅ Check if the class still has students
            $classHasStudents = Classes_Student::whereIn('classID', $classIDs)->exists();

            if (!$classHasStudents) {
                // If no students left, delete the class
                Classes::whereIn('id', $classIDs)->delete();
            }

            // Update class status to "Approved"
            Classes::where('id', $request->classID)->update(['status' => 'The Registrar approved the submitted grade of' . $selectedDepartment . 'department']);

            return redirect()->route('registrar_classes')->with('success', 'Final grades for ' . $selectedDepartment . ' have been submitted successfully!');
        }

        // ✅ Update only records matching classID and department
        DB::table('final_grade')
            ->where('classID', $request->classID)
            ->where('department', $request->department)
            ->update($updateData);
        return back()->with('success', 'Registrar’s decision has been submitted successfully!');
    }

    public function ClassesMenu()
{
    $departments = \App\Models\Department::all(); // fetch all departments
    return view('registrar.classes', compact('departments'));
}

}

