<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Helpers\MongoModel;

class UserRepository
{
  protected MongoModel $userModel;

  public function __construct()
  {
    $this->userModel = new MongoModel('users');
  }

  public function findById(string $id): ?array
  {
    return $this->userModel->find(['_id' => $id]);
  }

  public function save(array $vehicle): string
  {
    return $this->userModel->save($vehicle);
  }
}