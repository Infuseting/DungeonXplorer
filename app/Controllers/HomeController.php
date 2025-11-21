<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        // Load the view
        require_once __DIR__ . '/../Views/home.php';
    }
}
