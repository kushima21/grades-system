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
| STATIC VIEWS
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('welcome'));
Route::get('/dashboard', fn () => view('admin.dashboard'));
Route::get('/default', fn () => view('layouts.default'));
Route::get('/course', fn () => view('admin.course'));

// Instructor views
Route::get('/my_grades', fn () => view('instructor.my_grades'));
Route::get('/my_class', fn () => view('instructor.my_class'));
Route::get('/grading&score', fn () => view('instructor.grading&score'));

// Registrar views
Route::get('/my_class_archive', fn () => view('registrar.my_class_archive'));
Route::get('/classes_view', fn () => view('registrar.classes_view'));

// Users view
Route::get('/users', fn () => view('admin.users'));

// Departments view
Route::get('/departments', fn () => view('admin.departments'));

// Login view
Route::get('/login', fn () => view('auth.login'));


/*
|--------------------------------------------------------------------------
| COURSE ROUTES
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
Route::get('/classes', [DepartmentController::class, 'classesPage'])->name('registrar.classes');


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


/*
|--------------------------------------------------------------------------
| REGISTRAR → CREATE CLASS (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/registrar/classes', [RegistrarController::class, 'ClassesMenu'])->name('RegistrarClasses');
    Route::post('/registrar/classes/create', [RegistrarController::class, 'CreateClass'])->name('CreateClass');
});


/*
|--------------------------------------------------------------------------
| AUTH (GUEST ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');

    // Register security
    Route::post('/register/security', function (\Illuminate\Http\Request $request) {
        $securityPassword = 'register_password';

        if ($request->input('security_code') === $securityPassword) {
            session(['register_access' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Incorrect security code.'], 403);
    })->name('register.security');

    // Login & Register POST
    Route::post('/login', [AuthController::class, 'LoginPost'])->name('login.post');
    Route::post('/register', [AuthController::class, 'RegisterPost'])->name('register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Forgot/Reset Password
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

Route::get('/login', fn () => view('auth.login'));


/*
|--------------------------------------------------------------------------
| REGISTRAR CLASS MANAGEMENT
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
