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
Route::patch('/test-case', [TestCaseController::class, 'patchTestCase']);
Route::post('/test-step', [TestStepsController::class, 'postTestStep']);
Route::patch('/test-step', [TestStepsController::class, 'patchTestStep']);
Route::delete('/test-step', [TestStepsController::class, 'deleteTestStep']);
Route::post('/import-test-case', [TestStepsController::class, 'postImportedTestCase']);
Route::post('/step-order-change', [TestCaseTestStepOrderController::class, 'postStepOrderChange']);
Route::get('/test-case', [TestCaseController::class, 'getTestCase']);
Route::get('/test-case-search', [TestCaseController::class, 'getTestCaseSearch']);


