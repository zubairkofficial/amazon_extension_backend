<?php

use App\Http\Controllers\Api\ScrapeProductController;
use App\Http\Controllers\Admin\SelectorController;
use App\Http\Controllers\Api\ScapeCompareController;
use App\Http\Controllers\Api\ErpProductCompareController;
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
Route::post('testgptapi', [ScrapeProductController::class, 'testgptapi']);

Route::prefix('scrapeProduct')->group(function () {
    Route::post('save', [ScrapeProductController::class, 'save']);
});

Route::prefix('selectors')->group(function () {
    Route::get('all', [SelectorController::class, 'getall']);
});
Route::post('scrapecompare', [ScapeCompareController::class, "save"]);
Route::post('erpcompare', [ErpProductCompareController::class, "save"]);
