<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Respondent_response;

class Respondent_responses extends ResourceController
{
    protected $api_key;
    protected $respondentResponseModel;

    public function __construct()
    {
        $this->respondentResponseModel = new Respondent_response();
        $this->api_key = $_SERVER['HTTP_API_KEY'];
    }

    public function get()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $response_id = $this->request->getVar('response_id') ? : NULL;

        if ($response_id) {
            $responses = $this->respondentResponseModel
                            ->where('is_deleted', 0)
                            ->find($response_id);
        } else {
            $responses = $this->respondentResponseModel
                            ->where('is_deleted', 0)
                            ->findAll();
        }

        return $responses ? 
            $this->respond($responses) :
            $this->failNotFound("No responses found");
    }

    public function add()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $respondent_id = $this->request->getVar('respondent_id');
        $question_ids = $this->request->getVar('question_ids');
        $responses = $this->request->getVar('responses');

        $no_of_questions = count($question_ids);
        $no_of_responses = count($responses);

        if ($no_of_questions != $no_of_responses)
            return $this->fail('Responses lacking');

        $responses_data = [];
        for ($i=0; $i<$no_of_questions; $i++) {
            $responses_data[] = [
                'respondent_id' => $respondent_id,
                'question_id' => $question_ids[$i],
                'response' => $responses[$i],
                'added_on' => date("Y-m-d H:i:s"),
                'added_by' => $respondent_id
            ];
        }

        return (count($responses_data) > 0 AND !$this->respondentResponseModel->insertBatch($responses_data)) ?
            $this->failServerError('An error occurred. Please try again.') :
            $this->respond(['response' => 'Responses added successfully.']) ;
    }
}