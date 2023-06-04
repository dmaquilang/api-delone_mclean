<?php

namespace App\Models;

use CodeIgniter\Model;

class Login extends Model
{
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'user_id',
        'ip_address',
        'platform',
        'user_agent',
        'logged_on',
        'response_code',
        'response_message'
    ];
    
    public function __construct()
    {
        $this->table = 'login';
    }
}

?>