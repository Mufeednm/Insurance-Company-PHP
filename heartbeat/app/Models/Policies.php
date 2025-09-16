<?php

namespace App\Models;

use CodeIgniter\Model;

class Policies extends Model
{
    protected $table            = 'policies';
    protected $primaryKey       = 'policyId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
       "policyNumber", 'productId', "customerName","customerphone", 'status', 
        'created_at', 'modified_at'
    ];

    // timestamps
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    // validation
    protected $validationRules = [
        'productId' => 'required|is_natural_no_zero',
        'customerName'  => 'required|min_length[2]',
        'customerphone' => 'required|numeric|min_length[10]|max_length[15]',

        'status'    => 'required|in_list[Active,Expired]',
    ];

    protected $validationMessages = [
        'productId' => [
            'required' => 'Please select a Product.',
        ],
        'customerName' => [
            'required' => 'Customer name is required.',
        ],
        'customerphone' => [
            'required'   => 'Customer phone number is required.',
            'numeric'    => 'Customer phone number must contain digits only.',
            'min_length' => 'Phone number must be at least 10 digits.',
            'max_length' => 'Phone number cannot exceed 15 digits.',
        ],
        
        'status' => [
            'in_list' => 'Status must be Active or Expired.',
        ],
    ];

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
