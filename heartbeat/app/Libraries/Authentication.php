<?php
namespace App\Libraries;

class Authentication
{
    private $user;
    public function login($username, $password)
    {
        $model = new \App\Models\Login;
        $roles = new \App\Models\Roles;
        $rolePermissions = new \App\Models\RolePermissions;

        $user = $model->where('userName', $username)->first();

        if($user === null)
        {
            return false;
        }
        else if($user->status != "Active")
        {
            return false;
        }
        else
        {
            if(md5($password) != $user->password)
            {
               return false;
            } 
        }

        $db      = \Config\Database::connect();
        $role = $roles->findById($user->roleId);
        $isAdmin = $role->isAdmin ? $role->isAdmin : 0;
        $allPermissions = $rolePermissions->getPermissionsForUser($user->loginId); 

        $session = session();
        $session->regenerate();
        
        $session->set('loginId', $user->loginId);
        $session->set('userName', $user->userName);
        $session->set('mobileNumber', $user->mobileNumber);
        $session->set('emailAddress', $user->emailAddress);
        $session->set('roleId', $user->roleId);
        $session->set('isAdmin', $isAdmin);
        $session->set('userPermissions', $allPermissions);
        return true;
    }

    public function logout()
    {
        session()->destroy();
        return true;
    }

    public function getCurrentUser()
    {
        if(! $this->isLoggedIn())
        {
            return null;
        }

        if($this->user === null)
        {
            $model = new \App\Models\Login();
            $this->user = $model->where('loginId', session()->get('loginId'));
        }

        return $this->user;
    }

    public function isLoggedIn()
    {
        return session()->has('loginId');
    }
}