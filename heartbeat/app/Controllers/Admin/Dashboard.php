<?php

namespace App\Controllers\Admin;
use App\Entities\Entity;

class Dashboard extends \App\Controllers\BaseController
{
    public function __construct()
    {
        include_once "heartbeat/app/Controllers/Models.php";
        session()->set('activate', "dashboard");
    }

    public function index()
    {
        return view('admin/dashboard');
    }
}
