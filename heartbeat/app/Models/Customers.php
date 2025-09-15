<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'customerId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'name', 'email', 'phone', 'whatsappNumber', 
        'created_at', 'modified_at'
    ];

    // timestamps
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    // validation
    protected $validationRules = [
        'name'  => 'required|min_length[2]',
        'email' => 'required|valid_email',
    ];
    
    protected $validationMessages = [
        'name' => [
            'required' => 'Customer name is required.',
        ],
        'email' => [
            'valid_email' => 'Please provide a valid email address.',
        ],
    ];
    

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
