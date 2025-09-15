<?php

namespace App\Controllers\Admin;

class Home extends \App\Controllers\BaseController
{
    public function __construct()
    {
        include_once "heartbeat/app/Controllers/Models.php";
    }

    public function index()
    {
        if (service('auth')->isLoggedIn())
        {
            return redirect()->to('admin/dashboard');
        }
        else
        {
            return redirect()->to("admin/login");
        }
    }
}
