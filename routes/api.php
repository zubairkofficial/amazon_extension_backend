<?php

use App\Http\Controllers\ScrapeProductController;
use App\Http\Controllers\SelectorController;
use App\Http\Controllers\ScrapingController;
use App\Http\Controllers\Api\AmazonScraperController;
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
Route::get('test',function(){
    return $data = ['match'=>'Yes','Reason'=>'reason'];
});
Route::get('scrape', [ScrapingController::class, "scrape"]);
Route::post('scrapeAmazon', [AmazonScraperController::class, "scrapeAmazon"]);
