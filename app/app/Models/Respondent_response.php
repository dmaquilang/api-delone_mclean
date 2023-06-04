<?php

namespace App\Models;

use CodeIgniter\Model;

class Respondent_response extends Model
{
    protected $table      = 'respondent_response';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';

    protected $allowedFields = [
        'respondent_id',
        'question_id',
        'response',
        'added_by',
        'added_on',
        'updated_by',
        'updated_on',
        'is_deleted'
    ];
}