<?php
namespace App\Domain\Auth\DTO;

class LogoutDTO
{
    public bool $invalidateSession = true;
    public bool $regenerateToken = true;

    public function __construct(bool $invalidateSession, bool $regenerateToken)
    {
        $this->invalidateSession = $invalidateSession;
        $this->regenerateToken = $regenerateToken;
    }

    public static function fromRequest($request): self
    {

        return new self(
            $request->input('invalidateSession', true),
            $request->input('regenerateToken', true)
        );
    }
}