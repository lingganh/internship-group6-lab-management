<?php

use App\Http\Controllers\admin\GroupController;
use App\Http\Controllers\admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeControler;
use App\Http\Controllers\Auth\AuthenticateController;

Route::get('auth/redirect',[AuthenticateController::class,'redirectToSSO'])->name('sso.redirect');
Route::get('auth/callback', [AuthenticateController::class,'handleSSOCallback'])->name('sso.callback');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');

Route::get('/', function () {
    return view('pages.client.home');
})->name('home');

Route::get('/event-calendar', [HomeControler::class, 'eventsCalendar'])->name('events.calendar');

Route::middleware('role:admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return view('pages.admin.dashboard');
        })->name('admin.dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
        });

        Route::prefix('groups')->group(function () {
            Route::get('/',[GroupController::class, 'index'] )->name('admin.groups.index');
            Route::get('/create',[GroupController::class, 'create'] )->name('admin.groups.create');
            Route::get('/edit/{id}',[GroupController::class, 'edit'] )->name('admin.groups.edit');
        });
    });
});




Route::get('coming-soon', fn () => view('coming-soon'))->name('admin.coming-soon');
