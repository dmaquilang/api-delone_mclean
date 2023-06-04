<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Question;

class Questions extends ResourceController
{
    protected $api_key;
    protected $questionModel;

    public function __construct()
    {
        $this->questionModel = new Question();
        $this->api_key = $_SERVER['HTTP_API_KEY'];
    }

    public function get_all()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $questions = $this->questionModel->where('is_deleted', 0)->findAll();
        return $this->respond($questions);
    }
}