<?php

namespace App\Domain\Users\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Users\Repository\UsersRepository;

use RuntimeException;

class ExistenceCheck {
    private UsersRepository $repository;

    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): bool
    {
        try{

            return $this->repository->existenceCheck($request->input('id'));
            
        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
