<?php

if(! function_exists('current_user'))
{
    function current_user()
    {
        $auth = service('auth');
        //$permissionsModel = new \App\Models\PermissionsModel();
        //$permissions = $permissionsModel->where('roleId', $auth->getCurrentUser()->loginId);
        //$auth->permissions = $permissions;
        return $auth->getCurrentUser();
    }
}