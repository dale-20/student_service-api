<?php

use App\Enums\RoleSlug;
use App\Http\Controllers\Api\V1\AcademicRecordController;
use App\Http\Controllers\Api\V1\AcademicTermController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CourseOfferingController;
use App\Http\Controllers\Api\V1\CurriculumController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\EnrollmentStatusController;
use App\Http\Controllers\Api\V1\GradeController;
use App\Http\Controllers\Api\V1\InstructorOfferingController;
use App\Http\Controllers\Api\V1\LookupController;
use App\Http\Controllers\Api\V1\PasswordController;
use App\Http\Controllers\Api\V1\ProgramController;
use App\Http\Controllers\Api\V1\SessionController;
use App\Http\Controllers\Api\V1\StudentAcademicRecordController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\StudentEnrollmentController;
use App\Http\Controllers\Api\V1\StudentProfileController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'store'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'active', 'throttle:api'])->group(function (): void {
        Route::get('auth/me', [SessionController::class, 'show']);
        Route::post('auth/logout', [SessionController::class, 'destroy']);
        Route::put('auth/password', [PasswordController::class, 'update']);

        Route::middleware('password.changed')->group(function (): void {
            Route::get('dashboard', DashboardController::class);

            Route::middleware('role:'.RoleSlug::Administrator->value)->group(function (): void {
                Route::apiResource('users', UserController::class)->except('destroy');
                Route::put('users/{user}/password', [UserPasswordController::class, 'update']);
            });

            Route::middleware('role:'.RoleSlug::Administrator->value.','.RoleSlug::Registrar->value)->group(function (): void {
                Route::get('lookups', LookupController::class);
                Route::apiResource('students', StudentController::class)->except('destroy');
                Route::apiResource('programs', ProgramController::class)->except('destroy');
                Route::put('programs/{program}/curriculum', [CurriculumController::class, 'update']);
                Route::apiResource('courses', CourseController::class)->except('destroy');
                Route::apiResource('academic-terms', AcademicTermController::class)->parameters(['academic-terms' => 'academicTerm'])->except('destroy');
                Route::apiResource('course-offerings', CourseOfferingController::class)->parameters(['course-offerings' => 'courseOffering'])->except('destroy');
                Route::get('enrollments', [EnrollmentController::class, 'index']);
                Route::post('enrollments', [EnrollmentController::class, 'store']);
                Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show']);
                Route::patch('enrollments/{enrollment}/status', [EnrollmentStatusController::class, 'update']);
                Route::get('students/{student}/academic-record', [AcademicRecordController::class, 'show']);
            });

            Route::middleware('role:'.RoleSlug::Administrator->value.','.RoleSlug::Registrar->value.','.RoleSlug::Instructor->value)->group(function (): void {
                Route::get('course-offerings/{courseOffering}/grades', [GradeController::class, 'index']);
                Route::put('course-offerings/{courseOffering}/grades', [GradeController::class, 'update']);
            });

            Route::middleware('role:'.RoleSlug::Student->value)->group(function (): void {
                Route::get('me/student', [StudentProfileController::class, 'show']);
                Route::get('me/enrollments', [StudentEnrollmentController::class, 'index']);
                Route::get('me/academic-record', [StudentAcademicRecordController::class, 'show']);
            });

            Route::middleware('role:'.RoleSlug::Instructor->value)->get('me/course-offerings', [InstructorOfferingController::class, 'index']);
        });
    });
});
