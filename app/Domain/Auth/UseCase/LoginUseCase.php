<?php

namespace App\Domain\Auth\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Auth\Repository\AuthRepository;
// Entities
use App\Domain\Auth\Entity\Login\LoginEntity;

// DTOs
use App\Domain\Auth\DTO\LoginDTO;

use RuntimeException;

class LoginUseCase {
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): LoginEntity
    {
        try{
            $dto = LoginDTO::fromRequest($request);
            return $this->repository->login($dto);

        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
