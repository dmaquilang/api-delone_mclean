<?php

namespace App\Models;

use CodeIgniter\Model;

class Role extends Model
{
    protected $table      = 'role';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'name',
        'added_by',
        'added_on',
        'updated_by',
        'updated_on',
        'is_deleted'
    ];
}