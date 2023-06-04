<?php

namespace App\Models;

use CodeIgniter\Model;

class Question extends Model
{
    protected $table      = 'question';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'question',
        'added_by',
        'added_on',
        'updated_by',
        'updated_on',
        'is_deleted'
    ];
}