<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\VehicleRepository;

class VehicleService
{
  protected $vehicleRepository;

  public function __construct(VehicleRepository $vehicleRepository)
  {
    $this->vehicleRepository = $vehicleRepository;
  }

  public function getVehicleStock(): array
  {
    return $this->vehicleRepository->getAll();
  }

  public function findById(string $id): ?array
  {
    return $this->vehicleRepository->findById($id);
  }

  public function sellVehicle(array $vehicle): string
  {
    $vehicle['terjual'] = true;
    return $this->vehicleRepository->save($vehicle);
  }

  public function addVehicle(array $requestData): string
  {
    $type = $requestData['tipe_kendaraan'];

    if ($type === 'motor') {
      $vehicle = $this->addMotorVehicle($requestData);
    } else if ($type === 'mobil') {
      $vehicle = $this->addCarVehicle($requestData);
    }

    $vehicle['terjual'] = false;
    return $this->vehicleRepository->save($vehicle);
  }

  private function addMotorVehicle(array $requestData): array
  {
    $vehicle['motor'] = [
      'mesin' => $requestData['mesin'],
      'tipe_suspensi' => $requestData['tipe_suspensi'],
      'tipe_transmisi' => $requestData['tipe_transmisi'],
    ];

    return $this->addGeneralVehicleDetails($requestData, $vehicle, 'motor');
  }

  private function addCarVehicle(array $requestData): array
  {
    $vehicle['motor'] = [
      'mesin' => $requestData['mesin'],
      'kapasitas_penumpang' => $requestData['kapasitas_penumpang'],
      'tipe' => $requestData['tipe'],
    ];

    return $this->addGeneralVehicleDetails($requestData, $vehicle, 'mobil');
  }

  private function addGeneralVehicleDetails(array $requestData, array $vehicle, string $type): array
  {
    $vehicle['tahun_keluaran'] = $requestData['tahun_keluaran'];
    $vehicle['warna'] = $requestData['warna'];
    $vehicle['harga'] = $requestData['harga'];
    $vehicle['tipe_kendaraan'] = $requestData['tipe_kendaraan'];

    return $vehicle;
  }

  public function countSalesByVehicleType(): array
  {
    $result = $this->vehicleRepository->countSalesByVehicleType();
    $data = [];

    foreach ($result as $value) {
      $type = $value['tipe_kendaraan'];

      $data[$type] = [
        'terjual' => $value['terjual'],
        'tersisa' => $value['tersisa'],
        'total' => $value['total'],
      ];
    }

    return $data;
  }
}