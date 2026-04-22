<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRegisterCourseController;
use App\Http\Controllers\ComplainsController;
use App\Http\Controllers\DonthuController;
use App\Http\Controllers\NguonTinController;
use App\Http\Controllers\DonviController;
use App\Http\Controllers\VuanController;
 


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
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/register',[LoginController::class,'register']);
    Route::apiResource('/users',UserController::class);
    Route::post('/change_password',[UserController::class,'changePassword']);

    Route::patch('/complains/{id}/assign', [ComplainsController::class, 'assignTo']);
    Route::patch('/complains/{id}/status', [ComplainsController::class, 'changeStatus']);
    Route::apiResource('/complains', ComplainsController::class);

    // API quản lý đơn thư: GET/POST /api/donthu, GET/PUT/PATCH/DELETE /api/donthu/{id}
    Route::apiResource('/donthu', DonthuController::class);

    Route::apiResource('/nguon_tin', NguonTinController::class);

    // API quản lý đơn vị: GET/POST /api/donvi, GET/PUT/PATCH/DELETE /api/donvi/{id}
    Route::apiResource('/donvi', DonviController::class);

    // API quản lý vụ án: GET/POST /api/vuan, GET/PUT/PATCH/DELETE /api/vuan/{id}
    Route::apiResource('/vuan', VuanController::class);
});
