<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Respondent;

class Respondents extends ResourceController
{
    protected $api_key;
    protected $respondentModel;

    public function __construct()
    {
        $this->respondentModel = new Respondent();
        $this->api_key = $_SERVER['HTTP_API_KEY'];
    }

    public function get()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $respondent_id = $this->request->getVar('respondent_id') ? : NULL;

        if ($respondent_id) {
            $respondents = $this->respondentModel
                            ->where('is_deleted', 0)
                            ->find($respondent_id);
        } else {
            $respondents = $this->respondentModel
                            ->where('is_deleted', 0)
                            ->findAll();
        }

        return $respondents ? 
            $this->respond($respondents) :
            $this->failNotFound("No respondents found");
    }

    public function validate_email()
    {

        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $email = $this->request->getVar('email');

        $respondent = $this->respondentModel
                            ->where('email', $email)
                            ->where('is_deleted', 0)
                            ->first();

        if (!$respondent) {
            if (!($respondent_id = $this->respondentModel->insert([
                'email' => $email,
                'added_by' => 0,
                'added_on' => date("Y-m-d H:i:s")
            ]))) {
                return $this->failServerError('An error occurred. Please try again.');
            } else {
                return $this->respond(['response' => 'Email successfully saved.', 'id' => $respondent_id]);
            }
        } else {
            return $this->fail("Email already exist.");   
        }
    }

    public function update($id = null)
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        

        // $email = $this->request->getVar('email');
        $respondent = $this->respondentModel
                            ->where('id', $id)
                            ->where('is_deleted', 0)
                            ->first();

        if ($respondent) {
            $role = $this->request->getVar('role');
            $sex = $this->request->getVar('sex');
            $age = $this->request->getVar('age');
            $year_college = $this->request->getVar('year_college');
            $minutes_spent = $this->request->getVar('minutes_spent');

            $values = [
                'role' => $role,
                'sex' => $sex,
                'age' => $age,
                'year_college' => $year_college,
                'minutes_spent' => $minutes_spent,
                'updated_on' => date("Y-m-d H:i:s"),
                'updated_by' => 0
            ];

            if ($this->respondentModel->update($respondent['id'], $values)) 
                $response = $this->respond(['response' => 'Details saved']);
            else
                $response = $this->failServerError("An error occurred. Please try again.");
        } else {
            $response = $this->failNotFound('Email not found.');
        }

        return $response;
    }
}