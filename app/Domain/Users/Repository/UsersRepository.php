<?php

namespace App\Domain\Users\Repository;
use App\Domain\Users\Entity\Users\UserEntity;
use Illuminate\Http\Request;

interface UsersRepository {

    public function existenceCheck(int $userId):bool;
    public function Register(Request $request):UserEntity;

}
