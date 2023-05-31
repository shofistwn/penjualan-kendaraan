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

  public function getAll() : array
  {
    $stocks = $this->vehicleModel->get([]);
    return $stocks;
  }

  public function findById(string $id) : array
  {
    $vehicle = $this->vehicleModel->find(['_id' => $id]);
    return $vehicle;
  }

  public function save(array $vehicle) : string
  {
    $vehicleId = $this->vehicleModel->save($vehicle);
    return $vehicleId;
  }
}