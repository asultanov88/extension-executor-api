<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TestStepsController;
use App\Http\Controllers\TestCaseTestStepOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/test-case', [TestCaseController::class, 'postTestCase']);
Route::post('/test-step', [TestStepsController::class, 'postTestStep']);
Route::post('/step-order-change', [TestCaseTestStepOrderController::class, 'postStepOrder']);
Route::get('/test-case', [TestCaseController::class, 'getTestCase']);

