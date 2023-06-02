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
    return $this->vehicleModel->get([]);
  }

  public function findById(string $id): ?array
  {
    return $this->vehicleModel->find(['_id' => $id]);
  }

  public function save(array $vehicle): string
  {
    return $this->vehicleModel->save($vehicle);
  }

  public function countSalesByVehicleType(): array
  {
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

    return $this->vehicleModel->count($filter);
  }
}