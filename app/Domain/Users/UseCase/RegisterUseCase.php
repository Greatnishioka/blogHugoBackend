<?php

namespace App\Domain\Users\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Users\Repository\UsersRepository;
use App\Domain\Users\Entity\Users\UserEntity;

use RuntimeException;

class RegisterUseCase {
    private UsersRepository $repository;

    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): UserEntity
    {
        try{

            return $this->repository->register($request);
            
        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
