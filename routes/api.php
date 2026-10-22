<?php
use App\Http\Controllers\Api\OtpController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\IdentityCardApiController;
use App\Http\Controllers\Api\PassportApiController;
use App\Http\Controllers\Api\BirthCertificateApiController;
use App\Http\Controllers\Api\DeathCertificateApiController;
use App\Http\Controllers\Api\FamilyCardApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\ServiceFeeApiController;
use App\Http\Controllers\Api\GovernorateApiController;
use App\Http\Controllers\Api\DistrictApiController;
use App\Http\Controllers\Api\ServiceRequestApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/



Route::prefix('otp')->group(function () {
    Route::post('/send', [OtpController::class, 'send'])
        ->middleware('throttle:5,1');

    Route::post('/verify', [OtpController::class, 'verify'])
        ->middleware('throttle:10,1');
});

// ═══════════════════════════════════════════════════════════
// Public Routes
// ═══════════════════════════════════════════════════════════
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

// ─── Service Fees (Public) ───
Route::get('/service-fees', [ServiceFeeApiController::class, 'index']);
Route::get('/service-fees/category/{category}', [ServiceFeeApiController::class, 'byCategory']);
Route::get('/service-fees/{serviceKey}', [ServiceFeeApiController::class, 'show']);


// ─── Locations (Public) ───
Route::get('/governorates', [GovernorateApiController::class, 'index']);
Route::get('/governorates/all-with-districts', [GovernorateApiController::class, 'allWithDistricts']);
Route::get('/governorates/{governorateId}/districts', [GovernorateApiController::class, 'districts']);
Route::get('/governorates/{governorateId}/branches', [GovernorateApiController::class, 'branches']);
Route::get('/districts', [DistrictApiController::class, 'index']);
Route::get('/districts/{id}', [DistrictApiController::class, 'show']);

// ═══════════════════════════════════════════════════════════
// Protected Routes
// ═══════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ─── Auth ───
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/upload', [AuthController::class, 'uploadDocument']);

Route::get('/service-requests/by-category/{category}', [ServiceRequestApiController::class, 'byCategory']);
        // ─── Service Requests ───
    Route::get('/service-requests', [ServiceRequestApiController::class, 'index']);
    Route::post('/service-requests', [ServiceRequestApiController::class, 'store']);
    Route::get('/service-requests/{id}', [ServiceRequestApiController::class, 'show']);
    Route::post('/service-requests/{id}/cancel', [ServiceRequestApiController::class, 'cancel']);

    // ─── Appointments ───
    Route::get('/appointments', [AppointmentApiController::class, 'index']);
    Route::post('/appointments', [AppointmentApiController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentApiController::class, 'show']);
    Route::put('/appointments/{id}', [AppointmentApiController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentApiController::class, 'destroy']);

    // ─── Identity Cards ───
    Route::get('/identity-cards', [IdentityCardApiController::class, 'index']);
    Route::post('/identity-cards', [IdentityCardApiController::class, 'store']);
    Route::get('/identity-cards/{id}', [IdentityCardApiController::class, 'show']);

    // ─── Passports ───
    Route::get('/passports', [PassportApiController::class, 'index']);
    Route::post('/passports', [PassportApiController::class, 'store']);
    Route::get('/passports/{id}', [PassportApiController::class, 'show']);

    // ─── Birth Certificates ───
    Route::get('/birth-certificates', [BirthCertificateApiController::class, 'index']);
    Route::post('/birth-certificates', [BirthCertificateApiController::class, 'store']);
    Route::get('/birth-certificates/{id}', [BirthCertificateApiController::class, 'show']);

    // ─── Death Certificates ───
    Route::get('/death-certificates', [DeathCertificateApiController::class, 'index']);
    Route::post('/death-certificates', [DeathCertificateApiController::class, 'store']);
    Route::get('/death-certificates/{id}', [DeathCertificateApiController::class, 'show']);

    // ─── Family Cards ───
    Route::get('/family-cards', [FamilyCardApiController::class, 'index']);
    Route::post('/family-cards', [FamilyCardApiController::class, 'store']);
    Route::get('/family-cards/{id}', [FamilyCardApiController::class, 'show']);
    // ─── Notifications ───
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationApiController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationApiController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationApiController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationApiController::class, 'destroy']);
});