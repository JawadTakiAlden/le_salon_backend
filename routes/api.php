<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
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

Route::post('/login' , [AuthenticationController::class , 'login']);



Route::get('/meals' , [MealController::class , 'index']);
Route::get('/topMeals' , [MealController::class , 'topMeals']);
Route::post('/logout' , [AuthenticationController::class , 'logout']);
Route::get('/meals/{meal}' , [MealController::class , 'show']);
Route::get('/categories' , [CategoryController::class , 'index']);
Route::get('/categories/{category}' , [CategoryController::class , 'show']);
Route::get('/floors' , [FloorController::class , 'index']);
Route::get('/floors/{floor}' , [FloorController::class , 'show']);
Route::get('/tables' , [TableController::class , 'index']);
Route::get('/tables/{table}' , [TableController::class , 'show']);
Route::get('/users' , [UserController::class , 'index']);
Route::get('/users/{user}' , [UserController::class , 'show']);



Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/statistics' , [StatisticsController::class , 'index']);
    Route::get('/orders/{order}' , [OrderController::class , 'show']);
    Route::get('/orders' , [OrderController::class , 'index']);
    Route::post('/orders' , [OrderController::class , 'store']);
    Route::patch('/order-to-runner/{order}' , [OrderController::class , 'moveToRunner']);
    Route::patch('/order-to-casher/{order}' , [OrderController::class , 'moveToCasher']);
    Route::delete('/orders/{order}' , [OrderController::class , 'destroy']);
    Route::patch('/users/{user}' , [UserController::class , 'update']);
    Route::delete('/users/{user}' , [UserController::class , 'destroy']);
    Route::patch('/tables/{table}' , [TableController::class , 'update']);
    Route::delete('/tables/{table}' , [TableController::class , 'destroy']);
    Route::patch('/floors/{floor}' , [FloorController::class , 'update']);
    Route::delete('/floors/{floor}' , [FloorController::class , 'destroy']);
    Route::post('/meals/{meal}' , [MealController::class , 'update']);
    Route::delete('/meals/{meal}' , [MealController::class , 'destroy']);
    Route::patch('/switch_meal/{meal}' , [MealController::class , 'switchMeal']);
    Route::post('/categories/{category}' , [CategoryController::class , 'update']);
    Route::delete('/categories/{category}' , [CategoryController::class , 'destroy']);
    Route::post('/tables' , [TableController::class , 'store']);
    Route::post('/users' , [UserController::class , 'store']);
    Route::post('/meals' , [MealController::class , 'store']);
    Route::post('/floors' , [FloorController::class , 'store']);
    Route::post('/categories' , [CategoryController::class , 'store']);
    Route::get('/kitchen-orders' , [OrderController::class , 'kitchenOrders']);
    Route::get('/runner-orders' , [OrderController::class , 'runnerOrders']);
    Route::get('/casher-orders' , [OrderController::class , 'casherOrders']);
});

