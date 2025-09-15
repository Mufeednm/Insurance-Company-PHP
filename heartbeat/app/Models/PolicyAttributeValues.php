<?php

namespace App\Models;

use CodeIgniter\Model;

class PolicyAttributeValues extends Model
{
    protected $table            = 'policy_attribute_values';
    protected $primaryKey       = 'policyAttributeValueId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'policyId', 'attributeValueId', 
        'created_at', 'modified_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    protected $validationRules = [
        'policyId'         => 'required|is_natural_no_zero',
        'attributeValueId' => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'policyId' => [
            'required' => 'Policy ID is required.',
        ],
        'attributeValueId' => [
            'required' => 'Attribute Value ID is required.',
        ],
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
