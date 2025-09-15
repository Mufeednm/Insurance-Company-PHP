<?php

namespace App\Models;

use CodeIgniter\Model;

class AttributeValues extends Model
{
    protected $table            = 'attributes_values';
    protected $primaryKey       = 'attributeValueId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'policyId', 'attributeId', 'value', 
        'created_at', 'modified_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    protected $validationRules = [
        'policyId'    => 'required|is_natural_no_zero',
        'attributeId' => 'required|is_natural_no_zero',
        'value'       => 'required',
    ];

    protected $validationMessages = [
        'policyId' => [
            'required' => 'Policy ID is required.',
        ],
        'attributeId' => [
            'required' => 'Attribute ID is required.',
        ],
        'value' => [
            'required' => 'Value cannot be empty.',
        ],
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
