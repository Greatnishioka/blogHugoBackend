<?php

namespace App\Domain\Auth\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Auth\Repository\AuthRepository;
use App\Domain\Auth\Entity\UserAuth\UserAuthEntity;

use App\Domain\Auth\DTO\RegisterAuthDTO;

use RuntimeException;

class RegisterAuthUseCase {
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, int $id): UserAuthEntity
    {
        try{

            $dto = RegisterAuthDTO::fromRequest($request);
            return $this->repository->register($dto, $id);

        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
