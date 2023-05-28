<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TestStepsController;
use App\Http\Controllers\TestCaseTestStepOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\TestCaseExecutionController;

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

// Directories and Projects
Route::middleware(['auth','userProfile'])->get('/project-directories', [DirectoryController::class, 'getProjectsDirectories']);
Route::middleware(['auth','userProfile'])->post('/project', [DirectoryController::class, 'postProject']);
Route::middleware(['auth','userProfile'])->get('/projects', [DirectoryController::class, 'getAllProjects']);
Route::middleware(['auth','userProfile'])->post('/assign-user-project', [DirectoryController::class, 'postUserProject']);
Route::middleware(['auth','userProfile'])->post('/directory', [DirectoryController::class, 'postDirectory']);

// Test Case Execution.
Route::middleware(['auth','userProfile'])->post('/test-case-execution', [TestCaseExecutionController::class, 'postTestCaseExecution']);
Route::middleware(['auth','userProfile'])->get('/test-case-execution', [TestCaseExecutionController::class, 'getTestCaseExecution']);


