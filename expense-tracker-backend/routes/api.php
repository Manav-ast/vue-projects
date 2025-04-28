<?php

use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\GroupsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    //groups
    Route::get('/groups', [GroupsController::class, 'index'])->name('groups.index');
    Route::post('/groups/store', [GroupsController::class, 'store'])->name('groups.store');
    Route::delete('/groups/delete/{id}', [GroupsController::class, 'destroy'])->name('groups.delete');
    Route::patch('/groups/update/{id}', [GroupsController::class, 'update'])->name('groups.update');

    //expenses
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::post('/expenses/store', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/delete/{id}', [ExpensesController::class, 'destroy'])->name('expenses.delete');
    Route::patch('/expenses/update/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
});
