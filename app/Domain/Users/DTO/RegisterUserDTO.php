<?php
namespace App\Domain\Users\DTO;

class RegisterUserDTO
{
    public array $userAuth;
    public array $userData;

    public function __construct(array $userAuth, array $userData)
    {
        $this->userAuth = $userAuth;
        $this->userData = $userData;
    }

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('userAuth', []),
            $request->input('userData', [])
        );
    }
}