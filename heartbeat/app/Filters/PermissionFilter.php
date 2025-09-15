<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\Permissions;
use App\Models\Roles;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('loginId');
        $userRoleId = $session->get('roleId'); // assuming you're storing role ID in session

        if (!$userId) {
            return redirect()->to('admin/login');
        }

        // Admin bypass logic
        $roleModel = new Roles();
        $role = $roleModel->findById($userRoleId);

        if ($role && $role->isAdmin == 1) {
            // Admin → allow all permissions
            return; 
        }

        // Normal permission check for non-admin users
        $currentPath = strtolower($request->getPath());
        $permissionKey = $this->normalizePath($currentPath);
        
        $permissionModel = new Permissions();

        if (!$permissionModel->hasPermission($permissionKey))
        {
            $session->set('permissionKey', $permissionKey);  // optional: for debugging unauthorized page
            return redirect()->to('unauthorized');
        }
    }

    private function normalizePath($path)
    {
        $segments = explode('/', $path);
        
        $normalized = array_map(function($segment) {
            if (is_numeric($segment)) {
                return null;
            }
            if (strtolower($segment) == 'index') {
                return null;
            }
            return $segment;
        }, $segments);

        return implode('/', array_filter($normalized));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // do nothing
    }
}
