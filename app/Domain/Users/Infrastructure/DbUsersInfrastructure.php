<?php

namespace App\Domain\Users\Infrastructure;
use Illuminate\Http\Request;
// Models
use App\Models\User\User;
use App\Models\User\UserData;
use App\Models\User\UserOption;
use App\Models\User\UserStatus;
// Entities
use App\Domain\Users\Entity\UsersOption\UserOptionEntity;
use App\Domain\Users\Entity\UsersStatus\UserStatusEntity;
use App\Domain\Users\Entity\UserData\UserDataEntity;
use App\Domain\Users\Entity\Users\UserEntity;
// Repositories
use App\Domain\Users\Repository\UsersRepository;
// Others
use Illuminate\Support\Str;

class DbUsersInfrastructure implements UsersRepository
{
    private User $user;
    private UserData $userData;
    private UserOption $userOption;
    private UserStatus $userStatus;

    public function __construct(
        User $user,
        UserData $userData,
        UserOption $userOption,
        UserStatus $userStatus

    ) {
        $this->user = $user;
        $this->userData = $userData;
        $this->userOption = $userOption;
        $this->userStatus = $userStatus;
    }

    #[\Override]
    public function existenceCheck(int $userId): bool
    {
        return $this->user->where('id', $userId)->exists();
    }

    #[\Override]
    public function Register(Request $request): UserEntity
    {

        try {

            if ($this->user->where('email', $request->input('email'))->exists()) {
                throw new \RuntimeException('Email already exists.');
            }

            $savedUser = $this->registerMainUser();

            $userData = $this->registerUserData($request->input('userData'), $savedUser['id']);
            $userOption = $this->registerUserOption($request->input('userOption'), $savedUser['id']);
            $userStatus = $this->registerUserStatus($request->input('userStatus'), $savedUser['id']);

            return new UserEntity(
                $savedUser['id'],
                $savedUser['user_uuid'],
                new UserDataEntity(
                    $userData['user_id'],
                    $userData['name'],
                    $userData['icon_url'],
                    $userData['bio'],
                    $userData['occupation'],
                    $userStatus
                ),
                new UserOptionEntity(
                    $userOption['user_id'],
                    $userOption['option_id'],
                    $userOption['option_value']
                )
            );


        } catch (\Exception $e) {
            throw new \RuntimeException('Registration failed: ' . $e->getMessage());
        }
    }

    private function registerMainUser(): array
    {
        $user = $this->user->create([
            'user_uuid' => Str::uuid()->toString(),
        ]);

        return $user->getAttributes();
    }

    private function registerUserData($data, $id): array
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

    private function registerUserOption($option, $id): array
    {
        $savedOptions = [];

        foreach ($option as $op) {
            $savedOption = $this->userOption->create([
                'user_id' => $id,
                'option_id' => $op['optionId'],
                'option_value' => $op['optionValue'],
            ]);

            $savedOptions[] = new UserOptionEntity(
                $savedOption->getAttributes()['user_id'],
                $savedOption->getAttributes()['option_id'],
                $savedOption->getAttributes()['option_value']
            );
        }
        return $savedOptions;
    }

    private function registerUserStatus($status, $id): array
    {
        $savedStatuses = [];

        foreach ($status as $st) {
            $savedStatus = $this->userStatus->create([
                'user_id' => $id,
                'option_id' => $st['statusId'],
                'option_value' => $st['statusValue'],
            ]);

            $status = $savedStatus->getAttributes();

            $savedStatuses[] = new UserStatusEntity(
                $status['user_id'],
                $status['option_id'],
                $status['option_value']
            );
        }

        return $savedStatuses;
    }

}
