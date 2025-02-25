<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Import the default auth routes provided by Laravel
require __DIR__.'/auth.php';

// Enable user registration via Auth::routes() (already includes registration, login, etc.)
Auth::routes(['register' => true]);

// Redirect root to the dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// General authenticated routes (auth + email verification if needed)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile/{id}', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/updateProfile/{id}', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');

    // Student routes
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/edit/{student_id}', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/update/{student_id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/destroy/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}/classes', [StudentController::class, 'getStudentClasses'])->name('students.classes');
    Route::get('students/showProfile/{student_id}', [StudentController::class, 'showProfile'])->name('students.showProfile');

    // Attendance routes
    Route::get('students/attendance', [AttendanceController::class, 'showAttendance'])->name('students.attendance');
    Route::post('students/attendance/update', [AttendanceController::class, 'updateAttendance'])->name('students.updateAttendance');
    Route::post('students/attendance/filter', [AttendanceController::class, 'filterAttendance'])->name('students.attendance.filter');

    //Phone route
    Route::post('/phones', [PhoneController::class, 'store'])->name('phones.store');
    Route::delete('/phones/{id}', [PhoneController::class, 'destroy'])->name('phones.destroy');

    //Class route
    Route::get('/class',[ClassController::class, 'index'])-> name('students.class.class_index');
    Route::get('/class/create', [ClassController::class, 'create'])->name('students.class.class_create');
    Route::post('/class/store', [ClassController::class, 'store'])->name('students.class.class_store');
    Route::delete('/class/{id}/destroy', [ClassController::class, 'destroy'])->name('students.class.class_destroy');
    Route::get('/class/{id}/edit', [ClassController::class, 'edit'])->name('students.class.class_edit');
    Route::put('/class/{id}', [ClassController::class, 'update'])->name('students.class.update');

    //MessageController route
    Route::get('/students/messages', [MessageController::class, 'index'])->name('student.message.index');
    Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('message.send');

    //Payment route
    Route::get('/students/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('students/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('students/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::post('/payments/{payment}/void', [PaymentController::class, 'void'])->name('payments.void');
    Route::get('/receipt/{paymentId}', [PaymentController::class, 'showReceipt'])->name('receipt.show');
    Route::get('/invoice/{paymentId}', [PaymentController::class, 'showInvoice'])->name('invoice.show');

    //Coming Soon Page
    Route::get('/students/stillInProgress', [StudentController::class, 'stillInProgress'])->name('students.stillInProgress');

    //Show User
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('register', [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [UserController::class, 'register']);
});

// Home route (optional, depending on how you're using it)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
