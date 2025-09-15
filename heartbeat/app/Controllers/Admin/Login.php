<?php

namespace App\Controllers\Admin;

class Login extends \App\Controllers\BaseController
{
    public function index()
    {
        if(service('auth')->isLoggedIn())
        {
            return redirect()->to('admin/dashboard');
        }
        else
        {
            return view('admin/login');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return service("response")
                ->setStatusCode(403)
                ->setBody("You do not have permission to access this page directly");
        }

        $userName = $this->request->getPost('userName');
        $password = $this->request->getPost('password');
        $auth = service('auth');

        if($auth->login($userName, $password))
        {
            return redirect()->to('admin/dashboard')->with('success', 'Login Successful');
        }
        else
        {
            return redirect()->to('admin/login')->withInput()->with('warning', 'Invalid Credentials');
        }
    }

    public function logout()
    {
        service('auth')->logout();
        return redirect()->to('admin/login/showLogoutMessage');
    }

    public function showLogoutMessage()
    {
        return redirect()->to('admin/login')->with('reload', 'Logout Successful');
    }
}
