<?php

namespace App\Domain\Users\Repository;
use App\Domain\Users\Entity\Login\LoginEntity;
use App\Domain\Users\Entity\Logout\LogoutEntity;
use App\Domain\Users\Entity\Users\UserEntity;
use Illuminate\Http\Request;

interface UsersRepository {

    public function existenceCheck(int $userId):bool;
    public function Register(Request $request):UserEntity;

}
