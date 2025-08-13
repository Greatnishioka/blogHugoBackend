<?php

namespace App\Domain\Auth\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Auth\Repository\AuthRepository;

// DTOs
use App\Domain\Auth\DTO\ExistenceCheckEmailDTO;

use RuntimeException;

class ExistenceCheckEmailUseCase {
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): bool
    {
        try{
            $dto = ExistenceCheckEmailDTO::makeDTO($request->input('userAuth.email', ''));
            return $this->repository->existenceCheck($dto->email);

        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
