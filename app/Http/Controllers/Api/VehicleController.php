<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
      'message' => 'Dapatkan stok kendaraan',
      'data' => $vehicles
    ]);
  }

  public function sellVehicle(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'kendaraan_id' => 'required'
    ]);

    if ($validator->fails()) {
      return $this->validationErrorResponse($validator);
    }

    $vehicleId = $request->input('kendaraan_id');
    $vehicle = $this->findVehicleById($vehicleId);

    if (!$vehicle) {
      return $this->vehicleNotFoundResponse();
    }

    if ($vehicle['terjual']) {
      return $this->alreadySoldResponse();
    }

    $vehicleId = $this->vehicleService->sellVehicle($vehicle);
    $vehicle = $this->findVehicleById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Kendaraan terjual',
      'data' => $vehicle
    ]);
  }

  public function salesReport(): JsonResponse
  {
    $vehicles = $this->vehicleService->countSalesByVehicleType();

    return response()->json([
      'success' => true,
      'message' => 'Dapatkan laporan penjualan',
      'data' => $vehicles
    ]);
  }

  public function addMotorVehicle(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'tahun_keluaran' => 'required',
      'warna' => 'required',
      'harga' => 'required',
      'mesin' => 'required',
      'tipe_suspensi' => 'required',
      'tipe_transmisi' => 'required',
    ]);

    $request['tipe_kendaraan'] = 'motor';
    return $this->addVehicle($validator, $request);
  }

  public function addCarVehicle(Request $request): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'tahun_keluaran' => 'required',
      'warna' => 'required',
      'harga' => 'required',
      'mesin' => 'required',
      'kapasitas_penumpang' => 'required',
      'tipe' => 'required',
    ]);

    $request['tipe_kendaraan'] = 'mobil';
    return $this->addVehicle($validator, $request);
  }

  protected function addVehicle($validator, $request): JsonResponse
  {
    if ($validator->fails()) {
      return $this->validationErrorResponse($validator);
    }

    $requestData = $request->all();

    $vehicleId = $this->vehicleService->addVehicle($requestData);
    $vehicle = $this->findVehicleById($vehicleId);

    return response()->json([
      'success' => true,
      'message' => 'Kendaraan berhasil ditambahkan',
      'data' => $vehicle
    ]);
  }

  protected function findVehicleById($vehicleId)
  {
    return $this->vehicleService->findById($vehicleId);
  }

  protected function vehicleNotFoundResponse(): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => 'Kendaraan tidak ditemukan'
    ], 404);
  }

  protected function alreadySoldResponse(): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => 'Kendaraan sudah terjual'
    ], 404);
  }

  protected function validationErrorResponse($validator): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => 'Validasi gagal',
      'errors' => $validator->errors()
    ], 422);
  }
}