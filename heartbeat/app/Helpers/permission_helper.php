<?php

if (!function_exists('hasPermission')) {
    function hasPermission($permissionKey) {
        $isAdmin = session()->get('isAdmin');
        
        // If Admin — always allow everything
        if ($isAdmin == 1) {
            return true;
        }

        $userPermissions = session()->get('userPermissions') ?? [];
        return in_array($permissionKey, $userPermissions);
    }
}

