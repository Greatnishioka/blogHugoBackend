<?php
namespace App\Domain\Auth\DTO;

class LoginDTO
{
    public string $email;
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public static function fromRequest($request): self
    {
        $userAuth = $request->input('userAuth', []);

        return new self(
            $userAuth['email'] ?? '',
            $userAuth['password'] ?? ''
        );
    }
}