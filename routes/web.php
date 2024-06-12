<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LocalModelController;
use App\Http\Controllers\Admin\OpenAIModelController;
use App\Http\Controllers\Admin\imageCompareModelController;
use App\Http\Controllers\Admin\SelectorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth', 'user.check'])->group(function () {
    Route::get('/dashboard', function () { return view('home');})->name('dashboard');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'admin.check'])->prefix('/admin')->group(function () {

    Route::redirect('/', '/admin/dashboard')->name('admin.dashboard');
    Route::get('/admin_profile', [AdminController::class, 'admin_profile'])->name('admin.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.adminupdate');

    Route::get('/fetch-logs', [DashboardController::class, 'fetchLogs']);
    Route::get('/log/{id}', [DashboardController::class, 'show']);
    Route::get('/log/delete/{id}', [DashboardController::class, 'destroy']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::resource('/users', UserController::class);
    Route::resource('/admins', AdminController::class);
    Route::resource('/localmodels', LocalModelController::class);
    Route::resource('/openaimodels', OpenAIModelController::class);
    Route::resource('/imgCompModels', imageCompareModelController::class);

    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'update']);

    Route::get('selectors/index', [SelectorController::class, 'index'])->name('selectors.index');
    Route::get('selectors/create', [SelectorController::class, 'create'])->name('selectors.create');
    Route::post('selectors/store', [SelectorController::class, 'store'])->name('selectors.store');
    Route::get('selectors/{id}/edit', [SelectorController::class, 'edit'])->name('selectors.edit');
    Route::post('selectors/update/{id}', [SelectorController::class, 'update'])->name('selectors.update');
    Route::post('selectors/destroy/{id}', [SelectorController::class, 'destroy'])->name('selectors.destroy');
});
