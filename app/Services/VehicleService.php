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
    $vehicles = $this->vehicleRepository->getAll();
    return $vehicles;
  }

  public function findById(string $id): array
  {
    $vehicle = $this->vehicleRepository->findById($id);
    return $vehicle;
  }

  public function sellVehicle(array $vehicle): string
  {
    $vehicle['terjual'] = true;
    $vehicleId = $this->vehicleRepository->save($vehicle);
    return $vehicleId;
  }

  public function addVehicle(array $formData): string
  {
    $type = $formData['tipe_kendaraan'];
    if ($type === 'motor') {
      $motorcycle = $this->addMotorcycle($formData);
      $data['motor'] = $motorcycle;
    } else if ($type === 'mobil') {
      $car = $this->addCar($formData);
      $data['mobil'] = $car;
    }

    $data['tahun_keluaran'] = $formData['tahun_keluaran'];
    $data['warna'] = $formData['warna'];
    $data['harga'] = $formData['harga'];
    $data['tipe_kendaraan'] = $formData['tipe_kendaraan'];
    $data['terjual'] = false;

    $vehicleId = $this->vehicleRepository->save($data);
    return $vehicleId;
  }

  public function addMotorcycle(array $formData): array
  {
    $data['mesin'] = $formData['mesin'];
    $data['tipe_suspensi'] = $formData['tipe_suspensi'];
    $data['tipe_transmisi'] = $formData['tipe_transmisi'];

    return $data;
  }

  public function addCar(array $formData): array
  {
    $data['mesin'] = $formData['mesin'];
    $data['kapasitas_penumpang'] = $formData['kapasitas_penumpang'];
    $data['tipe'] = $formData['tipe'];

    return $data;
  }
}