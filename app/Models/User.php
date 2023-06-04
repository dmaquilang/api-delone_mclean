<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table      = 'user';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'full_name',
        'username',
        'password',
        'token',
        'api_key',
        'token_expiry',
        'added_by',
        'added_on',
        'updated_by',
        'updated_on',
        'is_deleted'
    ];
}