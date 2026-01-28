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
use App\Livewire\Admin\equipment\Index;
use App\Livewire\Admin\equipment\Create as EquipmentCreate;
use App\Livewire\Admin\equipment\Edit;
use App\Livewire\Approval;
use App\Livewire\UserSchedules;
use App\Livewire\LabRegister;
use App\Http\Controllers\client\EquipmentIssueController;
use App\Http\Controllers\admin\EquipmentIssueController as AdminEquipmentIssueController;
use App\Http\Controllers\admin\AdminNotificationController;
use App\Livewire\Client\EquipmentIssues\BulkCreate;
use App\Http\Controllers\admin\EquipmentIssueRequestController as AdminEquipmentIssueRequestController;
use App\Http\Controllers\NotificationController;

use App\Livewire\Admin\Lab\Index as LabIndex;
use App\Livewire\Admin\Lab\Create as LabCreate;
use App\Livewire\Admin\Lab\Edit as LabEdit;
use App\Exports\LabDiaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;


//login sso
Route::get('auth/redirect', [AuthenticateController::class, 'redirectToSSO'])->name('sso.redirect');
Route::get('auth/callback', [AuthenticateController::class, 'handleSSOCallback'])->name('sso.callback');
Route::post('/logout', [AuthenticateController::class, 'logout'])->name('handleLogout');

Route::get('login', [AuthenticateController::class, 'showLoginForm'])->name('login');
Route::get('register', [AuthenticateController::class, 'showRegisterForm'])->name('register');
Route::get('forgot-password', [AuthenticateController::class, 'forgotPassword'])->name('forgotPassword');
Route::get('set-password/{token}', [AuthenticateController::class, 'setPassword'])->name('setPassword');

Route::get('/', LabCalendar::class)->name('home');;

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
    Route::get('/thong-tin-tai-khoan', [ClientController::class, 'infoUser'])->name('client.info-user');
    Route::get('/doi-mat-khau', [ClientController::class, 'changePassword'])->name('client.change-password');
    Route::get('/danh-sach-nhom-nckh', [ClientController::class, 'GroupIndex'])->name('client.group-index');
    Route::get('/xac-thuc-2-lop', [ClientController::class, 'twoFactor'])->middleware(
        when(
            Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
            ['password.confirm'],
            [],
        ),
    )->name('client.two-factor');

    // Trang "Báo hỏng & lịch sử xử lý" theo thiết bị
    Route::get('/equipment/{equipment}/issues', [EquipmentIssueController::class, 'index'])
        ->name('client.equipment.issues.index');

    // Xử lý form gửi báo hỏng cho thiết bị
    Route::post('/equipment/{equipment}/issues', [EquipmentIssueController::class, 'store'])
        ->name('client.equipment.issues.store');
});

Route::middleware('role:admin')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return view('pages.admin.dashboard');
        })->name('admin.dashboard');
        Route::get('/report', function () {
            return view('pages.admin.report');
        })->name('admin.report');

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
        });

        Route::prefix('groups')->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('admin.groups.index');
            Route::get('/create', [GroupController::class, 'create'])->name('admin.groups.create');
            Route::get('/edit/{id}', [GroupController::class, 'edit'])->name('admin.groups.edit');
        });
        Route::get('/lab-diary', App\Livewire\LabDiary::class)->name('admin.lab-diary');
        Route::get('/approval', Approval::class)->name('admin.approval');
        Route::get('/equipment', Index::class)->name('admin.equipment.index');
        Route::get('/equipment/create', EquipmentCreate::class)->name('admin.equipment.create');
        Route::get('/equipment/edit/{id}', Edit::class)->name('admin.equipment.edit');
        Route::get('/lab-register', LabRegister::class)->name('lab.register');

        Route::prefix('equipment-issue-requests')->group(function () {
            // Danh sách phiếu báo hỏng
            Route::get('/', [AdminEquipmentIssueRequestController::class, 'index'])
                ->name('admin.equipment-issue-requests.index');

            // Chi tiết phiếu báo hỏng (
            Route::get('/{equipmentIssueRequest}', [AdminEquipmentIssueRequestController::class, 'show'])
                ->name('admin.equipment-issue-requests.show');
        });

        // Cập nhật trạng thái báo hỏng
        Route::patch('/equipment-issues/{issue}/status', [AdminEquipmentIssueController::class, 'updateStatus'])
            ->name('admin.equipment-issues.update-status');

        // Admin xóa báo hỏng
        Route::delete('/equipment-issues/{issue}', [EquipmentIssueController::class, 'destroy'])
            ->name('client.equipment.issues.destroy');

        // Thông báo có báo hỏng đến admin
        Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])
            ->name('admin.notifications.mark-all-read');
        Route::get('/lab', LabIndex::class)->name('admin.lab.index');
//        Route::get('/lab/create', LabCreate::class)->name('admin.lab.create');
//        Route::get('/lab/edit/{id}', LabEdit::class)->name('admin.lab.edit');
        Route::get('/lab-setting', LabEdit::class)->name('admin.lab-setting');

        Route::get('/export-lab-diary', function () {
            $temp = Cache::get('lab-diary-export-' . auth()->id());
            $events = $temp[0];
            $start = $temp[1];
            $end = $temp[2];

            abort_if(!$events, 419);


            return Excel::download(
                new LabDiaryExport($events, $start, $end),
                'Nhat_ky_su_dung_Lab.xlsx'
            );
        })->name('lab-diary.export');
    });
});

Route::get('/equipment/issues/bulk-create', function () {
    return view('pages.client.equipment.issues.bulk-create');
})->middleware('auth')
    ->name('client.equipment.issues.bulk-create');

Route::middleware('auth')->post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
    ->name('notifications.mark-all-read');
Route::middleware('auth')->get('/notifications/{notification}/open', [NotificationController::class, 'open'])
    ->name('notifications.open');

Route::get('coming-soon', fn() => view('coming-soon'))->name('admin.coming-soon');
