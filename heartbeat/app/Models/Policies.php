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
       

        'status'    => 'required|in_list[Active,Expired]',
    ];

    protected $validationMessages = [
        'productId' => [
            'required' => 'Please select a Product.',
        ],
        'customerName' => [
            'required' => 'Customer name is required.',
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
