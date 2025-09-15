<?php

namespace App\Models;

use CodeIgniter\Model;

class Products extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'productId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'name', 'status', 'created_at', 'modified_at'
    ];

    // timestamps
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    // validation
    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[100]',
        'status' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Product name is required.',
            'min_length' => 'Product name must be at least 2 characters long.',
            'max_length' => 'Product name cannot exceed 100 characters.',
        ],
        'status' => [
            'in_list' => 'Status must be either 0 (Inactive) or 1 (Active).',
        ],
    ];

    // common helper
    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
}
