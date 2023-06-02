<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
  protected $userRepository;

  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function findById(string $id): ?array
  {
    return $this->userRepository->findById($id);
  }

  public function register(array $requestData): string
  {
    return $this->userRepository->save($requestData);
  }
}