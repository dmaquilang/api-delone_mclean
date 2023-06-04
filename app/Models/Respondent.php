<?php

namespace App\Models;

use CodeIgniter\Model;

class Respondent extends Model
{
    protected $table      = 'respondent';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'email',
        'role',
        'sex',
        'age',
        'year_college',
        'minutes_spent',
        'added_by',
        'added_on',
        'updated_by',
        'updated_on',
        'is_deleted'
    ];
}