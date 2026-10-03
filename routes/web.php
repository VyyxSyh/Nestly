<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('auth-page');
})->middleware('guest')->name('login');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard-page');
    })->name('dashboard');

    Route::get('/tasks', function () {
        return view('tasks-page');
    })->name('tasks');

    Route::get('/schedules', function () {
        return view('schedules-page');
    })->name('schedules');

    Route::get('/subjects', function () {
        return view('subjects-page');
    })->name('subjects');

    Route::get('/finance', function () {
        return view('finance-page');
    })->name('finance');
});
