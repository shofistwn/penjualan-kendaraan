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
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::prefix('/v1')->group(function () {

  /**
   * Rute untuk login pengguna
   * route "/api/v1/auth/login"
   * @method POST
   */
  Route::post('/auth/login', [AuthController::class, 'login']);

  /**
   * Rute untuk mendaftarkan pengguna baru
   * route "/api/v1/auth/register"
   * @method POST
   */
  Route::post('/auth/register', [AuthController::class, 'register']);

  /*
   * Rute yang memerlukan autentikasi menggunakan JWT
   */
  Route::middleware('jwt.verify')->group(function () {

    /**
     * Rute untuk logout pengguna
     * route "/api/v1/auth/logout"
     * @method POST
     */
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('/vehicles')->group(function () {

      /**
       * Rute untuk menambahkan kendaraan baru
       * route "/api/v1/vehicles"
       * @method POST
       */
      Route::post('/', [VehicleController::class, 'addVehicle']);

      /**
       * Rute untuk mendapatkan stok kendaraan
       * route "/api/v1/vehicles/stock"
       * @method GET
       */
      Route::get('/stock', [VehicleController::class, 'getVehicleStock']);

      /**
       * Rute untuk menjual kendaraan
       * route "/api/v1/vehicles/sell"
       * @method POST
       */
      Route::post('/sell', [VehicleController::class, 'sellVehicle']);

      /**
       * Rute untuk laporan penjualan kendaraan
       * route "/api/v1/vehicles/sales-report"
       * @method GET
       */
      Route::get('/sales-report', [VehicleController::class, 'salesReport']);
    });
  });
});