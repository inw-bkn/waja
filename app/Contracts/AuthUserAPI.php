<?php

namespace App\Contracts;

interface AuthUserAPI
{
    public function getUser($login);

    public function authenticate($login, $password);
}
