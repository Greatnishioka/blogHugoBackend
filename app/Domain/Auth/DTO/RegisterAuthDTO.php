<?php
namespace App\Domain\Auth\DTO;

class RegisterAuthDTO
{
    public array $userAuth;

    public function __construct(array $userAuth)
    {
        $this->userAuth = $userAuth;
    }

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('userAuth', [])
        );
    }
}