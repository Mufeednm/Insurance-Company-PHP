<?php

namespace App\Models;

class Permissions extends \CodeIgniter\Model
{
    protected $table = 'permissions';
    protected $allowedFields = ["permissionKey", "description", "group", "created_at", "modified_at"];
    protected $returnType = 'App\Entities\Entity';
    protected $primaryKey = 'permissionId';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'modified_at';

    protected $validationRules = [
        'permissionKey' => 'required'
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

    public function hasPermission($permissionKey)
    {

        /* Database Based Check */
        $isAdmin = session()->get('isAdmin');

        if ($isAdmin == 1) {
            return true;
        }

        $roleId = session()->get('roleId');

        if (!$roleId) {
            return false;
        }

        $rolePermissionsModel = new \App\Models\RolePermissions();
        $permissionsModel = new \App\Models\Permissions();

        $permission = $permissionsModel
            ->select('permissions.permissionId')
            ->join('role_permissions', 'role_permissions.permissionId = permissions.permissionId')
            ->where('role_permissions.roleId', $roleId)
            ->where('permissions.permissionKey', $permissionKey)
            ->first();

        return $permission ? true : false;

        /* Database Based Check */

        /* Session Based Check */
        // $isAdmin = session()->get('isAdmin');
        // if ($isAdmin == 1) {
        //     return true;
        // }
        // $userPermissions = session()->get('userPermissions') ?? [];
        // return in_array($permissionKey, $userPermissions);
        /* Session Based Check */
    }
}
?>
