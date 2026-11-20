<?php

use App\Http\Controllers\PassportVerificationController;
use App\Http\Controllers\ReportFileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')
        ->name('settings.profile');

    Volt::route('settings/password', 'settings.password')
        ->name('settings.password');

    Volt::route('settings/appearance', 'settings.appearance')
        ->name('settings.appearance');

    Route::get(
        '/reports/{report}/view',
        [ReportFileController::class, 'show']
    )->name('reports.view');

    Route::get('/test-auth', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'guard' => auth()->getDefaultDriver(),
            'user_id' => auth()->id(),
        ]);
    });
});

Route::get(
    '/passports/verify/{passport}',
    [PassportVerificationController::class, 'show']
)->name('passports.verify');

require __DIR__ . '/auth.php';
