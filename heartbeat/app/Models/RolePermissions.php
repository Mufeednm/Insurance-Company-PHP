<?php

namespace App\Models;

class RolePermissions extends \CodeIgniter\Model
{
    protected $table = 'role_permissions';
    protected $allowedFields = ["roleId", "permissionId", "isAllowed", "created_at", "modified_at"];
    protected $returnType = 'App\Entities\Entity';
    
    protected $primaryKey = 'rolePermissionId';

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
	protected $updatedField = 'modified_at';
    
    protected $validationRules    = [
        'roleId' => 'required',
        'permissionId' => 'required'
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

    public function getPermissionsForUser($loginId)
    {
        $db = \Config\Database::connect();

        // First check if user is admin
        $builder = $db->table('login u');
        $builder->select('r.isAdmin');
        $builder->join('roles r', 'r.roleId = u.roleId');
        $builder->where('u.loginId', $loginId);
        $adminRow = $builder->get()->getRow();

        // If isAdmin = 1, return all permissions directly
        if ($adminRow && $adminRow->isAdmin == 1) {
            $permissionBuilder = $db->table('permissions')->select('permissionKey');
            $query = $permissionBuilder->get();
            return array_column($query->getResultArray(), 'permissionKey');
        }

        // Else fetch only assigned permissions
        $builder = $db->table('login u');
        $builder->select('p.permissionKey');
        $builder->join('roles r', 'r.roleId = u.roleId');
        $builder->join('role_permissions rp', 'rp.roleId = r.roleId');
        $builder->join('permissions p', 'p.permissionId = rp.permissionId');
        $builder->where('u.loginId', $loginId);
        $builder->where('rp.isAllowed', 1);
        $query = $builder->get();

        return array_column($query->getResultArray(), 'permissionKey');
    }
}

?>