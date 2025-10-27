<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeControler;

Route::get('/', function () {
    return view('pages.client.home');
})->name('home');

Route::get('/event-calendar', [HomeControler::class, 'eventsCalendar'])->name('events.calendar');

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('pages.admin.dashboard');
    })->name('admin.dashboard');
});

Route::get('coming-soon', fn () => view('coming-soon'))->name('admin.coming-soon');
