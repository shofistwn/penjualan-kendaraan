<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {

  Route::post('/auth/login', [AuthController::class, 'login']);
  Route::post('/auth/register', [AuthController::class, 'register']);

  Route::middleware('jwt.verify')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('/vehicles')->group(function () {
      Route::post('/add-motor', [VehicleController::class, 'addMotorVehicle']);
      Route::post('/add-car', [VehicleController::class, 'addCarVehicle']);
      Route::get('/stock', [VehicleController::class, 'getVehicleStock']);
      Route::post('/sell', [VehicleController::class, 'sellVehicle']);
      Route::get('/sales-report', [VehicleController::class, 'salesReport']);
    });
  });
});