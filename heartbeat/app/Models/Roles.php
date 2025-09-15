<?php

namespace App\Models;

class Roles extends \CodeIgniter\Model
{
    protected $table = 'roles';
    protected $allowedFields = ["roleName", "isAdmin", "created_at", "modified_at"];
    protected $returnType = 'App\Entities\Entity';
    
    protected $primaryKey = 'roleId';

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
	protected $updatedField = 'modified_at';
    
    protected $validationRules    = [
        'roleName' => 'required'
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
   
}

?>