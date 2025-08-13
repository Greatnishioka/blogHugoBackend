<?php

namespace App\Domain\Users\Repository;
use App\Domain\Users\Entity\Users\UserEntity;
use Illuminate\Http\Request;
use App\Domain\Users\DTO\RegisterUserDTO;

interface UsersRepository {

    public function existenceCheck(int $userId):bool;
    public function Register(RegisterUserDTO $dto):UserEntity;

}
