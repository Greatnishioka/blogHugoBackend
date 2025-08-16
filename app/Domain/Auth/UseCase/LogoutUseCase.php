<?php

namespace App\Domain\Auth\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Auth\Repository\AuthRepository;
// Entities
use App\Domain\Auth\Entity\Logout\LogoutEntity;

// DTOs
use App\Domain\Auth\DTO\LogoutDTO;

use RuntimeException;

class LogoutUseCase {
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): LogoutEntity
    {
        try{
            $dto = LogoutDTO::fromRequest($request);
            return $this->repository->logout($dto);

        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
