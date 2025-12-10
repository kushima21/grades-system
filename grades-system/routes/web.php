<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    IndexController,
    ForgotPasswordController,
    UserController,
    RegistrarController,
    InstructorController,
    AdminController,
    DeanController,
    CourseController,
    DepartmentController,
    ClassArchiveController,
    AllGradesController,
    StudentsGradeController,
    NotificationController,
    CompleteCredentialController,
    PDFController
};


/*
|--------------------------------------------------------------------------
| STATIC VIEW ROUTES
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');
Route::view('/dashboard', 'admin.dashboard');
Route::view('/default', 'layouts.default');
Route::view('/course', 'admin.course');

Route::view('/grading&score', 'instructor.grading&score');
Route::view('/grading_view', 'instructor.grading_view');
Route::view('/student_grades', 'instructor.student_grades');
Route::view('/student&grades_view', 'instructor.student&grades_view');

Route::view('/my_grades', 'instructor.my_grades');
Route::view('/my_class', 'instructor.my_class');
Route::get('/classes', fn () => view('registrar.classes'));

Route::view('/my_class_archive', 'registrar.my_class_archive');
Route::view('/classes_view', 'registrar.classes_view');

Route::view('/users', 'admin.users');
Route::view('/departments', 'admin.departments');
Route::view('/login', 'auth.login');


/*
|--------------------------------------------------------------------------
| COURSE MODULE ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/course/store', [CourseController::class, 'store'])->name('course.store');
Route::get('/courses', [CourseController::class, 'index'])->name('course.index');
Route::delete('/course/{id}/delete', [CourseController::class, 'destroy'])->name('course.destroy');
Route::put('/course/{id}/update', [CourseController::class, 'update'])->name('course.update');
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');


/*
|--------------------------------------------------------------------------
| DEPARTMENT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');


/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/users', [UserController::class, 'show'])->name('user.show');
Route::delete('/admin/users/delete', [UserController::class, 'destroy'])->name('user.destroy');
Route::get('/instructors/search', [UserController::class, 'searchInstructor'])->name('instructors.search');


/*
|--------------------------------------------------------------------------
| INSTRUCTOR ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/instructor_dashboard', [InstructorController::class, 'index'])->name('instructor');
Route::get('/instructor_classes', [InstructorController::class, 'classes'])->name('classes');
Route::get('/my_class', [InstructorController::class, 'index'])->name('instructor.my_class');

Route::get('/my_class_archive', [ClassArchiveController::class, 'index'])->name('instructor.my_class_archive');
Route::get('/grading&score', [InstructorController::class, 'grading'])->name('instructor.grading&score');
Route::get('/student_grades', [InstructorController::class, 'studentGrades'])->name('instructor.student_grades');

Route::get('/instructor/classes/{id}/grades', [RegistrarController::class, 'studentGradesView'])->name('instructor.student_grades_view');
Route::get('/instructor/classes/{id}/grades/{academic_period}', [RegistrarController::class, 'studentGradesView'])->name('student.grades.view');

Route::post('/initialize-grades', [RegistrarController::class, 'initializeGrades'])->name('initialize.grades');
Route::post('/lock-in-grades', [RegistrarController::class, 'lockInGrades'])->name('lock.grades');
Route::post('/submit-to-dean', [RegistrarController::class, 'submitToDean'])->name('submit.to.dean');


/*
|--------------------------------------------------------------------------
| REGISTRAR – CREATE CLASS (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/registrar/classes', [RegistrarController::class, 'ClassesMenu'])->name('RegistrarClasses');
    Route::post('/registrar/classes/create', [RegistrarController::class, 'CreateClass'])->name('CreateClass');
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (GUEST ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');

    Route::post('/register/security', function (\Illuminate\Http\Request $request) {
        $securityPassword = 'register_password';
        if ($request->input('security_code') === $securityPassword) {
            session(['register_access' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Incorrect security code.'], 403);
    })->name('register.security');

    Route::post('/login', [AuthController::class, 'LoginPost'])->name('login.post');
    Route::post('/register', [AuthController::class, 'RegisterPost'])->name('register.post');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});


/*
|--------------------------------------------------------------------------
| MAIN PAGE + LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login')->with('success', 'You have been logged out successfully.');
})->name('logout');


/*
|--------------------------------------------------------------------------
| REGISTRAR – FULL CLASS MANAGEMENT
|--------------------------------------------------------------------------
*/
Route::get('/registrar_dashboard', [RegistrarController::class, 'index'])->name('registrar');
Route::get('/classes', [RegistrarController::class, 'registrar_classes'])->name('registrar_classes');
Route::post('/classes', [RegistrarController::class, 'CreateClass'])->name('classes.create');
Route::put('/registrar_dashboard/{class}', [RegistrarController::class, 'EditClass'])->name('classes.update');
Route::delete('/registrar_dashboard/{class}', [RegistrarController::class, 'DeleteClass'])->name('classes.destroy');
Route::get('/classes/{class}', [RegistrarController::class, 'show'])->name('class.show');
Route::post('/classes/class={class}', [RegistrarController::class, 'addstudent'])->name('class.addstudent');
Route::delete('/classes/class={class}/student={student}', [RegistrarController::class, 'removestudent'])->name('class.removestudent');
Route::put('/classes/class={class}', [RegistrarController::class, 'addPercentageAndScores'])->name('class.addPercentageAndScores');
Route::get('/quizzesadded/class={class}', [RegistrarController::class, 'show'])->name('class.quizzes');
Route::put('/quizzesadded/class={class}', [RegistrarController::class, 'addQuizAndScore'])->name('class.addquizandscore');

Route::get('/grading_view/{id}/{academic_period}', [RegistrarController::class, 'showGrading'])->name('instructor.grading_view');
Route::get('/student&grades_view/{id}/{academic_period}', [RegistrarController::class, 'studentGradesView'])->name('instructor.student&grades_view');
Route::put('/grading_view/{class}/add-quiz-scores', [RegistrarController::class, 'addQuizAndScore'])->name('grading_view.addQuizAndScore');
Route::get('/grading/scores/{classId}/{term}', [RegistrarController::class, 'getStudentScores'])->name('grading_view.getStudentScores');
Route::put('/class/{class}/add-percentage-scores', [RegistrarController::class, 'addPercentageAndScores'])->name('class.addPercentageAndScores');
Route::put('/grading_view/{class}', [RegistrarController::class, 'addPercentageAndScores'])->name('grading_view.addPercentageAndScores');


// Submit to Registrar after Dean confirms
Route::post('/registrar-submit-grades', [RegistrarController::class, 'SubmitGradesRegistrar'])
    ->name('registrar_submit_grades');


    Route::post('/dean-decision', [RegistrarController::class, 'deanDecision'])
    ->name('dean.decision');

    Route::post('/submit-to-dean', [RegistrarController::class, 'submitToDean'])
    ->name('submit.to.dean');


Route::post('/class/{class}/import-csv', [RegistrarController::class, 'importCSV'])
    ->name('class.importcsv');



// Registrar Decision Route
Route::post('/registrar/decision', 
    [App\Http\Controllers\RegistrarController::class, 'submitDecisionRegistrar']
)->name('registrar.decision');

// Submit to Registrar Button Route
Route::post('/registrar-submit-grades', [RegistrarController::class, 'submitToRegistrar'])
    ->name('registrar_submit_grades');
