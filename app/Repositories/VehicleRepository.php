<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Helpers\MongoModel;

class VehicleRepository
{
  protected MongoModel $vehicleModel;

  public function __construct()
  {
    $this->vehicleModel = new MongoModel('kendaraan');
  }

  public function getAll(): array
  {
    // Mengambil semua data kendaraan dari koleksi "kendaraan" menggunakan MongoModel
    $stocks = $this->vehicleModel->get([]);
    return $stocks;
  }

  public function findById(string $id): ?array
  {
    // Mencari kendaraan berdasarkan ID di koleksi "kendaraan" menggunakan MongoModel
    $vehicle = $this->vehicleModel->find(['_id' => $id]);
    return $vehicle;
  }

  public function save(array $vehicle): string
  {
    // Menyimpan kendaraan ke koleksi "kendaraan" menggunakan MongoModel
    $vehicleId = $this->vehicleModel->save($vehicle);
    return $vehicleId;
  }

  public function countSalesByVehicleType(): array
  {
    // Menghitung penjualan berdasarkan tipe kendaraan
    $filter = [
      [
        '$group' => [
          '_id' => '$tipe_kendaraan',
          'terjual' => [
            '$sum' => [
              '$cond' => [
                ['$eq' => ['$terjual', true]],
                1,
                0
              ]
            ]
          ],
          'tersisa' => [
            '$sum' => [
              '$cond' => [
                ['$eq' => ['$terjual', false]],
                1,
                0
              ]
            ]
          ],
          'total' => [
            '$sum' => 1
          ]
        ]
      ],
      [
        '$project' => [
          'tipe_kendaraan' => '$_id',
          'terjual' => 1,
          'tersisa' => 1,
          'total' => 1,
          '_id' => 0
        ]
      ]
    ];

    $result = $this->vehicleModel->count($filter);
    return $result;
  }
}