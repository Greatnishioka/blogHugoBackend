<?php

namespace App\Domain\Auth\Infrastructure;

use App\Models\User\UserAuth;

// Entities
use App\Domain\Auth\Entity\UserAuth\UserAuthEntity;
// DTOs
use App\Domain\Auth\DTO\RegisterAuthDTO;
// Repositories
use App\Domain\Auth\Repository\AuthRepository;

class DbAuthInfrastructure implements AuthRepository
{

    private UserAuth $userAuth;

    public function __construct(
        UserAuth $userAuth
    ) {
        $this->userAuth = $userAuth;
    }

    #[\Override]
    public function register(RegisterAuthDTO $dto, int $id): UserAuthEntity
    {
        $userAuth = $this->registerUserAuth($dto->userAuth, $id);

        return new UserAuthEntity(
            userId: $id,
            email: $userAuth['email'],
            password: $userAuth['password']
        );
    }

    private function registerUserAuth(array $userAuth, int $id): array
    {
        $userAuth = $this->userAuth->create([
            'user_id' => $id,
            'email' => $userAuth['email'],
            'password' => bcrypt($userAuth['password']),
        ]);

        return $userAuth->getAttributes();
    }

    #[\Override]
    public function existenceCheck(string $email): bool
    {
        return $this->userAuth->where('email', $email)->exists();
    }
}