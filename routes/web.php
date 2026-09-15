<?php

use Illuminate\Support\Facades\Route;

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