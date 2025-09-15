<?php

namespace App\Models;

use CodeIgniter\Model;

class Policies extends Model
{
    protected $table            = 'policies';
    protected $primaryKey       = 'policyId';
    protected $returnType       = 'App\Entities\Entity';

    protected $allowedFields    = [
        'productId', 'startDate', 'endDate', 'status',"isReminder", 
        'created_at', 'modified_at'
    ];

    // timestamps
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'modified_at';

    // validation
    protected $validationRules = [
        'productId' => 'required|is_natural_no_zero',
        'startDate' => 'required|valid_date',
        'endDate'   => 'permit_empty|valid_date',
        'isReminder'    => 'required|in_list[0,1]',
        'status'    => 'required|in_list[Active,Expired]',
    ];

    protected $validationMessages = [
        'productId' => [
            'required' => 'Please select a Product.',
        ],
        'startDate' => [
            'required' => 'Start date is required.',
        ],
        'isReminder' => [
            'in_list' => 'isReminder must be 0 or 1.',
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
