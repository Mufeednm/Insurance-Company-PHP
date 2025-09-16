<?php

namespace App\Models;

use CodeIgniter\Model;

class Attributes extends Model
{
    protected $table            = 'attributes';
    protected $primaryKey       = 'attributeId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'productId', 'attributeName', 'attributeType', 'isRequired', 'attributeOrder', 'options',"isReminder",
        'created_at', 'modified_at'
    ];

    // timestamps
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    // validation
    protected $validationRules = [
        'productId'     => 'required|is_natural_no_zero',
        'attributeName' => 'required|min_length[1]|max_length[255]',
        'attributeType' => 'required|in_list[text,textarea,select,checkbox,radio,number,date]',
        'isRequired'    => 'required|in_list[0,1]',
        'isReminder'    => 'required|in_list[0,1]',
        'attributeOrder'=> 'permit_empty|is_natural',
    ];

    protected $validationMessages = [
        'productId' => [
            'required' => 'Please select a Product.',
            'is_natural_no_zero' => 'Invalid Product selection.',
        ],
        'attributeName' => [
            'required' => 'Attribute name is required.',
        ],
        'attributeType' => [
            'in_list' => 'Invalid attribute type.',
        ],
        'isRequired' => [
            'in_list' => 'isRequired must be 0 or 1.',
        ],
        'isReminder' => [
            'in_list' => 'isReminder must be 0 or 1.',
        ],
        'attributeOrder' => [
            'is_natural' => 'Order must be a natural number (0,1,2...).',
        ],
    ];

    // common helper per your pattern
    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
