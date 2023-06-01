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
    // Mengambil data stok kendaraan dari repository
    $vehicles = $this->vehicleRepository->getAll();
    return $vehicles;
  }

  public function findById(string $id): ?array
  {
    // Mencari kendaraan berdasarkan ID dari repository
    $vehicle = $this->vehicleRepository->findById($id);
    return $vehicle;
  }

  public function sellVehicle(array $vehicle): string
  {
    // Menandai kendaraan sebagai terjual
    $vehicle['terjual'] = true;

    // Menyimpan perubahan kendaraan ke repository
    $vehicleId = $this->vehicleRepository->save($vehicle);
    return $vehicleId;
  }

  public function addVehicle(array $requestData): string
  {
    $type = $requestData['tipe_kendaraan'];
    if ($type === 'motor') {
      // Menambahkan detail kendaraan motor
      $motorcycle = $this->addMotorVehicle($requestData);
      $data['motor'] = $motorcycle;
    } else if ($type === 'mobil') {
      // Menambahkan detail kendaraan mobil
      $car = $this->addCarVehicle($requestData);
      $data['mobil'] = $car;
    }

    // Menambahkan informasi umum kendaraan
    $data['tahun_keluaran'] = $requestData['tahun_keluaran'];
    $data['warna'] = $requestData['warna'];
    $data['harga'] = $requestData['harga'];
    $data['tipe_kendaraan'] = $requestData['tipe_kendaraan'];
    $data['terjual'] = false;

    // Menyimpan kendaraan ke repository
    $vehicleId = $this->vehicleRepository->save($data);
    return $vehicleId;
  }

  private function addMotorVehicle(array $requestData): array
  {
    // Menambahkan detail kendaraan motor
    $data['mesin'] = $requestData['mesin'];
    $data['tipe_suspensi'] = $requestData['tipe_suspensi'];
    $data['tipe_transmisi'] = $requestData['tipe_transmisi'];

    return $data;
  }

  private function addCarVehicle(array $requestData): array
  {
    // Menambahkan detail kendaraan mobil
    $data['mesin'] = $requestData['mesin'];
    $data['kapasitas_penumpang'] = $requestData['kapasitas_penumpang'];
    $data['tipe'] = $requestData['tipe'];

    return $data;
  }
  public function countSalesByVehicleType(): array
  {
    // Mengambil hasil perhitungan penjualan kendaraan dari repository
    $result = $this->vehicleRepository->countSalesByVehicleType();

    // Inisialisasi array untuk menyimpan hasil
    $data = [];

    // Melakukan iterasi pada hasil perhitungan
    foreach ($result as $value) {
      // Jika tipe kendaraan adalah 'motor'
      if ($value['tipe_kendaraan'] === 'motor') {
        // Menyimpan data penjualan kendaraan motor
        $data['motor'] = [
          'terjual' => $value['terjual'],
          'tersisa' => $value['tersisa'],
          'total' => $value['total'],
        ];
      } else {
        // Jika tipe kendaraan bukan 'motor' (dianggap 'mobil')
        // Menyimpan data penjualan kendaraan mobil
        $data['mobil'] = [
          'terjual' => $value['terjual'],
          'tersisa' => $value['tersisa'],
          'total' => $value['total'],
        ];
      }
    }

    // Mengembalikan data hasil perhitungan
    return $data;
  }
}