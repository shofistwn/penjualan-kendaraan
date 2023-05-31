<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
  protected $vehicleService;

  public function __construct(VehicleService $vehicleService)
  {
    $this->vehicleService = $vehicleService;
  }

  public function getVehicleStock(): JsonResponse
  {
    $vehicles = $this->vehicleService->getVehicleStock();
    return response()->json([
      'success' => true,
      'message' => 'Get vehicle stock',
      'data' => $vehicles
    ]);
  }

  public function sellVehicle(Request $request): JsonResponse
  {
    $validator = \Validator::make($request->all(), [
      'kendaraan_id' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    $vehicleId = $request->input('kendaraan_id');

    $vehicle = $this->vehicleService->findById($vehicleId);

    if (!$vehicle) {
      return response()->json([
        'success' => false,
        'message' => 'Vehicle not found'
      ], 404);
    }

    if ($vehicle['terjual']) {
      return response()->json([
        'success' => false,
        'message' => 'Vehicle has been sold'
      ], 404);
    }

    $vehicleId = $this->vehicleService->sellVehicle($vehicle);
    $vehicle = $this->vehicleService->findById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Vehicle sold successfully',
      'data' => $vehicle
    ]);
  }

  public function salesReport()
  {
    $vehicles = $this->vehicleService->getVehicleStock();

    $carsSold = 0;
    $carsRemaining = 0;
    $motorcyclesSold = 0;
    $motorcyclesRemaining = 0;
    $totalCars = 0;
    $totalMotorcycles = 0;

    foreach ($vehicles as $vehicle) {
      $isCar = $vehicle['tipe_kendaraan'] === 'mobil';
      $isMotorcycle = $vehicle['tipe_kendaraan'] === 'motor';
      $isSold = $vehicle['terjual'];

      if ($isCar) {
        if ($isSold) {
          $carsSold++;
        }
        $carsRemaining++;
        $totalCars++;
      }
      if ($isMotorcycle) {
        if ($isSold) {
          $motorcyclesSold++;
        }
        $motorcyclesRemaining++;
        $totalMotorcycles++;
      }
    }

    return response()->json([
      'success' => true,
      'message' => 'Get sales report',
      'data' => [
        'mobil' => [
          'terjual' => $carsSold,
          'tersisa' => $carsRemaining,
          'total' => $totalCars
        ],
        'motor' => [
          'terjual' => $motorcyclesSold,
          'tersisa' => $motorcyclesRemaining,
          'total' => $totalMotorcycles
        ]
      ]
    ]);
  }

  public function addVehicle(Request $request): JsonResponse
  {
    $validator = \Validator::make($request->all(), [
      'tahun_keluaran' => 'required',
      'warna' => 'required',
      'harga' => 'required',
      'tipe_kendaraan' => 'required',
      'mesin' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
      ], 422);
    }

    $formData = $request->all();
    $vehicleId = $this->vehicleService->addVehicle($formData);
    $vehicle = $this->vehicleService->findById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Success added vehicle',
      'data' => $vehicle
    ]);
  }
}