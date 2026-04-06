<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRegisterCourseController;
use App\Http\Controllers\ComplainsController;
use App\Http\Controllers\DonthuController;
use App\Http\Controllers\NguontinController;
 


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
Route::post('/login',[LoginController::class,'login']);
Route::post('/register',[LoginController::class,'register']);
Route::apiResource('/users',UserController::class);
Route::post('/change_password',[UserController::class,'changePassword']);

Route::patch('/complains/{id}/assign', [ComplainsController::class, 'assignTo']);
Route::patch('/complains/{id}/status', [ComplainsController::class, 'changeStatus']);
Route::apiResource('/complains', ComplainsController::class);

// API quản lý đơn thư: GET/POST /api/donthu, GET/PUT/PATCH/DELETE /api/donthu/{id}
Route::apiResource('/donthu', DonthuController::class);

Route::apiResource('/nguon_tin', NguontinController::class);
