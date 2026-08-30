<?php

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
});

require __DIR__.'/auth.php';
