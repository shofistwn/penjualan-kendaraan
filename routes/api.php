<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
  Route::post('auth/login', [AuthController::class, 'login']);

  Route::middleware('jwt.verify')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('vehicles', [VehicleController::class, 'getVehicleStock']);
    Route::post('vehicles/add', [VehicleController::class, 'addVehicle']);
    Route::post('vehicles/sell', [VehicleController::class, 'sellVehicle']);
    Route::post('vehicles/report', [VehicleController::class, 'salesReport']);
  });
});