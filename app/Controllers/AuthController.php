<?php

namespace App\Controllers;

class AuthController
{
    public function login()
    {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function register()
    {
        require_once __DIR__ . '/../Views/auth/register.php';
    }
}
