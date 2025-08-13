<?php

namespace App\Domain\Auth\Repository;
use App\Domain\Users\Entity\Users\UserEntity;
use App\Domain\Auth\DTO\RegisterAuthDTO;

// Entities
use App\Domain\Auth\Entity\UserAuth\UserAuthEntity;

interface AuthRepository {

    public function register(RegisterAuthDTO $dto, int $id):UserAuthEntity;
    public function existenceCheck(string $email): bool;

}
