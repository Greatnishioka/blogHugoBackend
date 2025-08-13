<?php

namespace App\Domain\Users\Infrastructure;

// Models
use App\Models\User\User;
use App\Models\User\UserData;
use App\Models\User\UserOption;
use App\Models\User\UserStatus;

use App\Models\Options\Option;
use App\Models\Status\Status;
use App\Models\User\UserAuth;
// Entities
use App\Domain\Users\Entity\UserOption\UserOptionEntity;
use App\Domain\Users\Entity\UserStatus\UserStatusEntity;
use App\Domain\Users\Entity\UserData\UserDataEntity;
use App\Domain\Users\Entity\Users\UserEntity;
// Repositories
use App\Domain\Users\Repository\UsersRepository;
// DTOs
use App\Domain\Users\DTO\RegisterUserDTO;
// Others
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DbUsersInfrastructure implements UsersRepository
{
    private User $user;
    private UserData $userData;
    private UserOption $userOption;
    private UserStatus $userStatus;
    private Option $option;
    private Status $status;
    private UserAuth $userAuth;

    public function __construct(
        User $user,
        UserData $userData,
        UserOption $userOption,
        UserStatus $userStatus,
        Option $option,
        Status $status,
        UserAuth $userAuth
    ) {
        $this->user = $user;
        $this->userData = $userData;
        $this->userOption = $userOption;
        $this->userStatus = $userStatus;
        $this->option = $option;
        $this->status = $status;
        $this->userAuth = $userAuth;
    }

    #[\Override]
    public function existenceCheck(int $userId): bool
    {
        return $this->user->where('id', $userId)->exists();
    }

    #[\Override]
    public function Register(RegisterUserDTO $dto): UserEntity
    {

        return DB::transaction(function () use ($dto) {
 
            $userAuthDTO = $dto->userAuth;
            
            // e-mailはダブリなし
            if ($this->userAuth->where(column: 'email', operator: $userAuthDTO['email'])->exists()) {
                throw new \RuntimeException('Email already exists.');
            }

            $savedUser = $this->registerMainUser();

            $userData = $this->registerUserData($dto->userData, $savedUser['id']);
            $userOption = $this->registerUserOption($savedUser['id']);
            $userStatus = $this->registerUserStatus($savedUser['id']);

            return new UserEntity(
                $savedUser['id'],
                $savedUser['user_uuid'],
                new UserDataEntity(
                    $userData['user_id'],
                    $userData['name'],
                    $userData['icon_url'],
                    $userData['bio'],
                    $userData['occupation'],
                ),
                $userOption,
                $userStatus
            );
        });
    }

    private function registerMainUser(): array
    {
        $user = $this->user->create([
            'user_uuid' => Str::uuid()->toString(),
        ]);

        return $user->getAttributes();
    }

    private function registerUserData(array $data, int $id): array
    {
        $userData = $this->userData->create([
            'user_id' => $id,
            'name' => $data['name'] ?? null,
            'icon_url' => $data['icon_url'] ?? null,
            'bio' => $data['bio'] ?? null,
            'occupation' => $data['occupation'] ?? null,
        ]);

        return $userData->getAttributes();
    }

    private function registerUserOption(int $id): array
    {
        $savedOptions = [];

        $options = $this->option->all();

        foreach ($options as $op) {
            $savedOption = $this->userOption->create([
                'user_id' => $id,
                'option_id' => $op['id'],
                'option_value' => false,
            ]);

            $option = $savedOption->getAttributes();

            $savedOptions[] = new UserOptionEntity(
                $option['user_id'],
                $option['option_id'],
                $option['option_value']
            );
        }
        return $savedOptions;
    }

    private function registerUserStatus(int $id): array
    {
        $savedStatuses = [];

        $status = $this->status->all();

        foreach ($status as $st) {
            $savedStatus = $this->userStatus->create([
                'user_id' => $id,
                'status_id' => $st['id'],
                'status_value' => false,
            ]);

            $status = $savedStatus->getAttributes();

            $savedStatuses[] = new UserStatusEntity(
                $status['user_id'],
                $status['status_id'],
                $status['status_value']
            );
        }

        return $savedStatuses;
    }
}
