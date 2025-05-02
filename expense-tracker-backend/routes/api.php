<?php

use App\Http\Controllers\GroupsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatGPTController;
use App\Http\Controllers\AiCommandController;
use App\Http\Controllers\CommandController;

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

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    // Group routes
    Route::prefix('groups')->group(function () {
        Route::get('/', [GroupsController::class, 'index'])->name('groups.index');
        Route::post('/store', [GroupsController::class, 'store'])->name('groups.store');
        Route::delete('/delete/{id}', [GroupsController::class, 'destroy'])->name('groups.delete');
        Route::patch('/update/{id}', [GroupsController::class, 'update'])->name('groups.update');
    });

    // Expense routes
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpensesController::class, 'index'])->name('expenses.index');
        Route::post('/store', [ExpensesController::class, 'store'])->name('expenses.store');
        Route::delete('/delete/{id}', [ExpensesController::class, 'destroy'])->name('expenses.delete');
        Route::patch('/update/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
        Route::get('/export-csv', [ExpensesController::class, 'exportCsv']);
        Route::get('/export-pdf', [ExpensesController::class, 'exportPdf']);
    });

    // ChatGPT routes
    Route::post('/chatgpt/command', [ChatGPTController::class, 'processCommand']);

    // AI command routes
    Route::post('/ai/command', [AiCommandController::class, 'processCommand']);

    Route::post('/command', [CommandController::class, 'handle']);
});
