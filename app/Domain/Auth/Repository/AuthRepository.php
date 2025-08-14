<?php

namespace App\Domain\Auth\Repository;
use App\Domain\Auth\Entity\Login\LoginEntity;

// DTOs
use App\Domain\Auth\DTO\LoginDTO;
use App\Domain\Auth\DTO\RegisterAuthDTO;

// Entities
use App\Domain\Auth\Entity\UserAuth\UserAuthEntity;

interface AuthRepository {

    public function register(RegisterAuthDTO $dto, int $id):UserAuthEntity;
    public function existenceCheck(string $email): bool;
    public function login (LoginDTO $dto): LoginEntity;

}
