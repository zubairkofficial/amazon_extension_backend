<?php

use App\Http\Controllers\ScrapeProductController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/webhook', [WebhookController::class, 'handle']);
Route::post('testcode', [ScrapeProductController::class, 'testcode']);
Route::post('testwebhook', [ScrapeProductController::class, 'testwebhook']);

Route::prefix('scrapeProduct')->group(function () {
    Route::post('save', [ScrapeProductController::class, 'save']);
});
