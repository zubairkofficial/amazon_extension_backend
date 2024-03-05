<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\GPTKeyController;
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
// Route::get('/dashboard', function () {
//     return view('home', ['logs' => Log::all()]);
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'user.check'])->group(function () {
    Route::get('/dashboard', function () {
        return view('home', ['logs' => Log::where('user_id', Auth::user()->id)->get()]);
    })->name('dashboard');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/log/{id}', [SettingController::class, 'show']);
    Route::get('/log/delete/{id}', [SettingController::class, 'destroy']);
    // Route::get('/admin', [SettingController::class, 'index']);
    Route::get('/setting', [SettingController::class, 'setting'])->name('setting');
    // Route::post('/updateKey', [GPTKeyController::class, 'update'])->name('gptKey.update');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'admin.check'])->prefix('/admin')->group(function () {
    Route::redirect('/', '/admin/dashboard')->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('/users', UserController::class);
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'update']);
});