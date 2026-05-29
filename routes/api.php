<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CertificateApiController;
use App\Http\Controllers\Api\V1\CourseApiController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\StudentApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('auth/login',  [AuthController::class, 'login'])->name('auth.login');
    Route::get ('certificates/verify/{code}', [CertificateApiController::class, 'verify'])->name('certificates.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get ('me',          MeController::class)->name('me');

        Route::apiResource('students', StudentApiController::class);
        Route::apiResource('courses',  CourseApiController::class);

        Route::get('certificates',               [CertificateApiController::class, 'index'])
            ->middleware('permission:certificates.view')
            ->name('certificates.index');
        Route::get('certificates/{certificate}', [CertificateApiController::class, 'show'])
            ->middleware('permission:certificates.view')
            ->name('certificates.show');
    });
});
