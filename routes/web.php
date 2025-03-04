<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\LogSheetController;
use Illuminate\Support\Facades\Auth;
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');


Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    } else {
        return redirect('/admin/login');
    }
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/log/sheets/view', [LogSheetController::class, 'view'])->name('log_sheets.view');
    Route::get('/log/sheets/download', [LogSheetController::class, 'download'])
        ->name('log_sheets.download');

    // Route::redirect('settings', 'settings/profile');
    // Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    // Volt::route('settings/password', 'settings.password')->name('settings.password');
    // Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
