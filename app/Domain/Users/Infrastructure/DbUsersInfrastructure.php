<?php

namespace App\Domain\Users\Infrastructure;
use Illuminate\Http\Request;
// Models
use App\Models\User\User;
use App\Models\User\UserData;
use App\Models\User\UserOption;
use App\Models\User\UserStatus;

use App\Models\Options\Option;
use App\Models\Status\Status;
use App\Models\User\UserAuth;
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
    public function Register(Request $request): UserEntity
    {

        try {

            if ($this->userAuth->where('email', $request->input('email'))->exists()) {
                throw new \RuntimeException('Email already exists.');
            }

            $savedUser = $this->registerMainUser();

            $userData = $this->registerUserData($request->input('userData'), $savedUser['id']);
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
                    $userStatus
                ),
                $userOption,
                $userStatus
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

    private function registerUserOption($id): array
    {
        $savedOptions = [];

        $options = $this->option->all();

        foreach ($options as $op) {
            $savedOption = $this->userOption->create([
                'user_id' => $id,
                'option_id' => $op['id'],
                // 初期値を入れたいのでoption_valueは設定しない
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

    private function registerUserStatus($id): array
    {
        $savedStatuses = [];

        $status = $this->status->all();

        foreach ($status as $st) {
            $savedStatus = $this->userStatus->create([
                'user_id' => $id,
                'status_id' => $st['id'],
                // 初期値を入れたいのでstatus_valueは設定しない
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
