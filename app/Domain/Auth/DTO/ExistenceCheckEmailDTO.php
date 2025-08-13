<?php
namespace App\Domain\Auth\DTO;

class ExistenceCheckEmailDTO
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function makeDTO(string $email): self
    {
        return new self(
            $email
        );
    }
}