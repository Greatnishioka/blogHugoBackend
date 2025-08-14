<?php

namespace App\Domain\Auth\Infrastructure;

// Entities
use App\Domain\Auth\Entity\UserAuth\UserAuthEntity;
use App\Domain\Auth\Entity\Login\LoginEntity;
// Models
use App\Models\User\User;
use App\Models\User\UserAuth;
// DTOs
use App\Domain\Auth\DTO\RegisterAuthDTO;
use App\Domain\Auth\DTO\LoginDTO;
// Repositories
use App\Domain\Auth\Repository\AuthRepository;
// Others
use Illuminate\Support\Facades\Auth;

class DbAuthInfrastructure implements AuthRepository
{

    private User $user;
    private UserAuth $userAuth;


    public function __construct(
        User $user,
        UserAuth $userAuth
    ) {
        $this->user = $user;
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

    #[\Override]
    public function existenceCheck(string $email): bool
    {
        return $this->userAuth->where('email', $email)->exists();
    }

    #[\Override]
    public function login(LoginDTO $dto): LoginEntity
    {
        try {
            $credentials = [
                'email' => $dto->email,
                'password' => $dto->password,
            ];

            if (Auth::attempt($credentials)) {
                session()->regenerate();

                // ログインしたユーザー情報を取得
                $user = Auth::user();

                $userData = $this->user->where('id', $user->id)->firstOrFail();

                return new LoginEntity(
                    id: $userData->id,
                    userUuid: $userData->user_uuid
                );
            }
            else {
                throw new \RuntimeException('ログインに失敗しました。', 0);
            }

        } catch (\Exception $e) {
            throw new \RuntimeException('ログインに失敗しました。', 0, $e);
        }
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
}