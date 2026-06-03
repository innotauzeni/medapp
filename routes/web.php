<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\HeroSlideController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\BookingController as PublicBookingController;
use App\Http\Controllers\Public\CartController as PublicCartController;
use App\Http\Controllers\Public\HomeController as PublicHomeController;
use App\Http\Controllers\Public\PublicCourseController;
use App\Http\Controllers\Public\TrackingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// ============================================================
// Public site (no auth required)
// ============================================================
Route::get('/', PublicHomeController::class)->name('home');

Route::get('/courses',                [PublicCourseController::class, 'index'])->name('public.courses');
Route::get('/courses/{course}',       [PublicCourseController::class, 'show'])->name('public.course');

// Cart
Route::get   ('/cart',                       [PublicCartController::class, 'show'])->name('public.cart');
Route::post  ('/cart/add',                   [PublicCartController::class, 'add'])->name('public.cart.add');
Route::patch ('/cart/{key}',                 [PublicCartController::class, 'update'])->name('public.cart.update');
Route::delete('/cart/{key}',                 [PublicCartController::class, 'remove'])->name('public.cart.remove');
Route::delete('/cart',                       [PublicCartController::class, 'clear'])->name('public.cart.clear');
Route::post  ('/cart/sync',                  [PublicCartController::class, 'sync'])->name('public.cart.sync');

// Booking
Route::get ('/book',                         [PublicBookingController::class, 'showForm'])->name('public.book.form');
Route::post('/book',                         [PublicBookingController::class, 'store'])->name('public.book.store');
Route::get ('/book/confirmation/{code}',     [PublicBookingController::class, 'confirmation'])->name('public.book.confirmation');

// Booking tracking
Route::get ('/track',                        [TrackingController::class, 'form'])->name('track.form');
Route::post('/track',                        [TrackingController::class, 'submit'])->name('track.submit');
Route::get ('/track/{code}',                 [TrackingController::class, 'show'])->name('track.show');

// Public certificate verification (existing)
Route::get ('/verify',                       [VerificationController::class, 'form'])->name('verify.form');
Route::post('/verify',                       [VerificationController::class, 'submit'])->name('verify.submit');
Route::get ('/verify/{code}',                [VerificationController::class, 'show'])->name('verify.show');

// ============================================================
// Authenticated admin / staff area  (URIs prefixed with /admin to
// avoid collisions with the public /courses catalogue, etc.
// Route NAMES stay the same so Blade route('students.index') etc. keep working.)
// ============================================================
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings (admin)
    Route::middleware('permission:bookings.view')->group(function () {
        Route::get   ('bookings',                       [BookingController::class, 'index'])->name('bookings.index');
        Route::get   ('bookings/{booking}',             [BookingController::class, 'show'])->name('bookings.show');
        Route::patch ('bookings/{booking}/status',      [BookingController::class, 'updateStatus'])->name('bookings.status');
        Route::patch ('bookings/{booking}/assign',      [BookingController::class, 'assign'])->name('bookings.assign');
        Route::post  ('bookings/{booking}/note',        [BookingController::class, 'addNote'])->name('bookings.note');
    });

    // Students
    Route::middleware('permission:students.view')->group(function () {
        Route::resource('students', StudentController::class);
        Route::post('students/{student}/archive', [StudentController::class, 'archive'])->name('students.archive');
        Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore');
    });

    // Courses
    Route::middleware('permission:courses.view')->group(function () {
        Route::post('courses/ai-generate', [CourseController::class, 'aiGenerate'])->name('courses.ai-generate');
        Route::resource('courses', CourseController::class);
    });

    // Course categories (admin CRUD)
    Route::middleware('permission:categories.view')->group(function () {
        Route::resource('categories', CourseCategoryController::class)->except(['show']);
    });

    // Locations
    Route::middleware('permission:locations.view')->group(function () {
        Route::resource('locations', LocationController::class)->except(['show']);
    });

    // Schedules
    Route::middleware('permission:schedules.view')->group(function () {
        Route::resource('schedules', CourseScheduleController::class)->except(['show']);
    });

    // Trainers
    Route::middleware('permission:trainers.view')->group(function () {
        Route::resource('trainers', TrainerController::class);
    });

    // Enrolments + assessment + attendance
    Route::middleware('permission:enrolments.view')->group(function () {
        Route::resource('enrolments', EnrolmentController::class)->except(['destroy']);
        Route::post('enrolments/{enrolment}/attendance',         [EnrolmentController::class, 'recordAttendance'])->name('enrolments.attendance');
        Route::post('enrolments/{enrolment}/assessment',         [EnrolmentController::class, 'recordAssessment'])->name('enrolments.assessment');
        Route::post('enrolments/{enrolment}/complete',           [EnrolmentController::class, 'complete'])->name('enrolments.complete');
        Route::post('enrolments/{enrolment}/issue-certificate',  [CertificateController::class, 'issueFromEnrolment'])->name('enrolments.issue-certificate');
    });

    // Certificates
    Route::middleware('permission:certificates.view')->group(function () {
        Route::get   ('certificates',                        [CertificateController::class, 'index'])->name('certificates.index');
        Route::get   ('certificates/create',                 [CertificateController::class, 'create'])->name('certificates.create');
        Route::post  ('certificates',                        [CertificateController::class, 'store'])->name('certificates.store');
        Route::get   ('certificates/{certificate}',          [CertificateController::class, 'show'])->name('certificates.show');
        Route::get   ('certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
        Route::patch ('certificates/{certificate}/revoke',   [CertificateController::class, 'revoke'])->name('certificates.revoke');
    });

    // Reports
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Hero slides
    Route::middleware('permission:hero_slides.view')->group(function () {
        Route::resource('hero_slides', HeroSlideController::class)->except(['show']);
    });

    // Users
    Route::middleware('permission:users.view')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Roles & permissions (admin-managed access control)
    Route::middleware('permission:roles.view')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
