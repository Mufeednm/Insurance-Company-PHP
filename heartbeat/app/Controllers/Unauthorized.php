<?php

namespace App\Controllers;


class Unauthorized extends BaseController
{
    public function __construct()
    {
        include_once "heartbeat/app/Controllers/Models.php";
    }

    public function index()
    {
        return view('unauthorized');
    }
}
