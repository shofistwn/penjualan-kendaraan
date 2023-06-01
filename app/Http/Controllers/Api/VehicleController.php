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
    // Mengambil data stok kendaraan
    $vehicles = $this->vehicleService->getVehicleStock();

    return response()->json([
      'success' => true,
      'message' => 'Dapatkan stok kendaraan',
      'data' => $vehicles
    ]);
  }

  public function sellVehicle(Request $request): JsonResponse
  {
    // Validasi input
    $validator = \Validator::make($request->all(), [
      'kendaraan_id' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Validasi gagal',
        'errors' => $validator->errors()
      ], 422);
    }

    // Mendapatkan ID kendaraan dari request
    $vehicleId = $request->input('kendaraan_id');

    // Cek keberadaan kendaraan
    $vehicle = $this->vehicleService->findById($vehicleId);

    if (!$vehicle) {
      return response()->json([
        'success' => false,
        'message' => 'Kendaraan tidak ditemukan'
      ], 404);
    }

    // Cek apakah kendaraan sudah terjual
    if ($vehicle['terjual']) {
      return response()->json([
        'success' => false,
        'message' => 'Kendaraan sudah terjual'
      ], 404);
    }

    // Jual kendaraan
    $vehicleId = $this->vehicleService->sellVehicle($vehicle);
    $vehicle = $this->vehicleService->findById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Kendaraan terjual',
      'data' => $vehicle
    ]);
  }

  public function salesReport()
  {
    // Mendapatkan data stok kendaraan
    $vehicles = $this->vehicleService->getVehicleStock();

    // Menghitung laporan penjualan kendaraan
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
        } else {
          $carsRemaining++;
        }
        $totalCars++;
      }
      if ($isMotorcycle) {
        if ($isSold) {
          $motorcyclesSold++;
        } else {
          $motorcyclesRemaining++;
        }
        $totalMotorcycles++;
      }
    }

    // Mengembalikan laporan penjualan kendaraan
    return response()->json([
      'success' => true,
      'message' => 'Dapatkan laporan penjualan',
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
    // Validasi input
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
        'message' => 'Validasi gagal',
        'errors' => $validator->errors()
      ], 422);
    }

    // Mendapatkan data kendaraan dari request
    $requestData = $request->all();

    // Menambahkan kendaraan
    $vehicleId = $this->vehicleService->addVehicle($requestData);
    $vehicle = $this->vehicleService->findById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Kendaraan berhasil ditambahkan',
      'data' => $vehicle
    ]);
  }
}