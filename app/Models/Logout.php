<?php

namespace App\Models;

use CodeIgniter\Model;

class Logout extends Model
{
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'user_id',
        'login_id',
        'logged_out_on'
    ];
    
    public function __construct()
    {
        $this->table = 'logout';
    }
}

?>