<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TestStepsController;
use App\Http\Controllers\TestCaseTestStepOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::middleware(['auth','userProfile'])->post('/test-case', [TestCaseController::class, 'postTestCase']);
Route::middleware(['auth','userProfile'])->patch('/test-case', [TestCaseController::class, 'patchTestCase']);
Route::middleware(['auth','userProfile'])->post('/test-step', [TestStepsController::class, 'postTestStep']);
Route::middleware(['auth','userProfile'])->patch('/test-step', [TestStepsController::class, 'patchTestStep']);
Route::middleware(['auth','userProfile'])->delete('/test-step', [TestStepsController::class, 'deleteTestStep']);
Route::middleware(['auth','userProfile'])->post('/import-test-case', [TestStepsController::class, 'postImportedTestCase']);
Route::middleware(['auth','userProfile'])->post('/step-order-change', [TestCaseTestStepOrderController::class, 'postStepOrderChange']);
Route::middleware(['auth','userProfile'])->get('/test-case', [TestCaseController::class, 'getTestCase']);
Route::middleware(['auth','userProfile'])->get('/test-case-search', [TestCaseController::class, 'getTestCaseSearch']);

// User
Route::middleware(['auth','userProfile'])->post('/user', [UserController::class, 'postUser']);


