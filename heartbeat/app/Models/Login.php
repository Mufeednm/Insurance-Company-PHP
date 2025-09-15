<?php

namespace App\Models;
class Login extends \CodeIgniter\Model
{
    protected $table = 'login';
    protected $allowedFields = ['userName', 'password', 'mobileNumber', 'emailAddress', 'otp', 'roleId', "pushToken", "status", "created_at", "modified_at"];

    protected $returnType = 'App\Entities\Entity';
    protected $primaryKey = 'loginId';
    protected $createdField = 'created_at';
	protected $updatedField = 'modified_at';
    protected $useTimestamps = true;

    protected $validationRules    = [

        'userName' => 'required|is_unique[login.userName]',
        'password' => 'required|min_length[6]',
        'password_confirmation' => 'required|matches[password]'

    ];
    
    protected $validationMessages = [
        'userName' => [
            'required' => 'Please enter username',
            'is_unique' => 'This username already exists in the system',
        ],
        'password' => [
            'required' => 'Please enter a password',
            'min_length' => 'Password should be at least 6 characters',
        ],
        'password_confirmation' => [
            'required' => 'Please enter the Password again',
            'matches' => 'Passwords do not match',
        ],
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if(isset($data['data']['password']))
        {
            $data['data']['password'] = md5($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    public function disablePasswordValidation()
    {
        unset($this->validationRules['userPassword']);
        unset($this->validationRules['password_confirmation']);
    }

    public function findById($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }

}
?>