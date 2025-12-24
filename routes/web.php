<?php

use App\Http\Controllers\admin\GroupController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\client\UserController as ClientController;
use App\Http\Livewire\LabCalendar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeControler;
use App\Http\Controllers\Auth\AuthenticateController;
use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use App\Livewire\Admin\Equipment\Index;
use App\Livewire\Admin\Equipment\Create;
use App\Livewire\Admin\Equipment\Edit;
use App\Livewire\Approval;
use App\Livewire\UserSchedules;

//login sso
Route::get('auth/redirect',[AuthenticateController::class,'redirectToSSO'])->name('sso.redirect');
Route::get('auth/callback', [AuthenticateController::class,'handleSSOCallback'])->name('sso.callback');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');

Route::get('login', [AuthenticateController::class, 'showLoginForm'])->name('login');
Route::get('register', [AuthenticateController::class, 'showRegisterForm'])->name('register');
Route::get('forgot-password', [AuthenticateController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('set-password/{token}', [AuthenticateController::class, 'setPassword'])->name('setPassword');

Route::get('/', LabCalendar::class )->name('home');;

Route::get('bookings', [LabCalendar::class, 'getAllBookings']);

Route::middleware('auth')->group(function () {
    Route::post('bookings', [LabCalendar::class, 'store']);
    Route::put('bookings/{id}', [LabCalendar::class, 'update']);
    Route::delete('bookings/{id}', [LabCalendar::class, 'destroy']);
    Route::patch('bookings/{id}/approve', [LabCalendar::class, 'approve']);
    Route::get('/my-schedules', UserSchedules::class)->name('user.schedules');
});

Route::get('/event-calendar', [HomeControler::class, 'eventsCalendar'])->name('events.calendar');

Route::middleware('checkAuth')->group(function () {
    Route::get('/thong-tin-tai-khoan',[ClientController::class,'infoUser'])->name('client.info-user');
    Route::get('/doi-mat-khau',[ClientController::class,'changePassword'])->name('client.change-password');
    Route::get('/xac-thuc-2-lop',[ClientController::class,'twoFactor'])->middleware(
        when(
            Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
            ['password.confirm'],
            [],
        ),
    )->name('client.two-factor');

});

Route::middleware('role:admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return view('pages.admin.dashboard');
        })->name('admin.dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
        });

        Route::prefix('groups')->group(function () {
            Route::get('/',[GroupController::class, 'index'] )->name('admin.groups.index');
            Route::get('/create',[GroupController::class, 'create'] )->name('admin.groups.create');
            Route::get('/edit/{id}',[GroupController::class, 'edit'] )->name('admin.groups.edit');
        });
        Route::get('/lab-diary', App\Livewire\LabDiary::class)->name('admin.lab-diary');
        Route::get('/approval', Approval::class)->name('admin.approval');
        Route::get('/equipment', Index::class)->name('equipment.index');

    });
});



Route::get('coming-soon', fn () => view('coming-soon'))->name('admin.coming-soon');
