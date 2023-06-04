<?php

namespace App\Models;

use CodeIgniter\Model;

class User_Log extends Model
{
    protected $table      = 'user_log';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'ip_address',
        'user_agent',
        'platform',
        'token',
        'token_expiry',
        'type',
        'time_check',
        'response'
    ];
}