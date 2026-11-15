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

    Route::get('/users/profile-photo/{path}', function (string $path) {
        $path = 'users/profile-photos/' . $path;

        abort_unless(
            Storage::disk('local')->exists($path),
            404
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    })
        ->where('path', '.*')
        ->name('users.profile-photo');

    Route::get('/citizens/photo/{path}', function (string $path) {
        $path = 'citizens/photos/' . $path;

        abort_unless(
            Storage::disk('local')->exists($path),
            404
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    })
        ->where('path', '.*')
        ->name('citizens.photo');

    Route::get('/citizens/face/{path}', function (string $path) {
        $path = 'citizens/faces/' . $path;

        abort_unless(
            Storage::disk('local')->exists($path),
            404
        );

        return response()->file(
            Storage::disk('local')->path($path)
        );
    })
        ->where('path', '.*')
        ->name('citizens.face');

    Route::get('/identity-cards/witnesses/{filename}', function (string $filename) {
        abort_unless(
            auth()->user()->can('identity_cards.view'),
            403
        );

        abort_unless(
            $filename === basename($filename)
            && preg_match('/\A[a-zA-Z0-9._-]+\.(jpg|jpeg|png)\z/i', $filename),
            404
        );

        $path = 'identity-cards/witnesses/' . $filename;

        abort_unless(
            Storage::disk('private')->exists($path),
            404
        );

        return response()->file(
            Storage::disk('private')->path($path)
        );
    })->name('identity-cards.witness-image');

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
