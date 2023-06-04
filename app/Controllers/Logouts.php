<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Logout;
use App\Models\Api_response;

use CodeIgniter\RESTful\ResourceController;

class Logouts extends ResourceController
{
    public function __construct()
    {
        $this->api_key = $_SERVER['HTTP_API_KEY'];
        $this->userModel = new User();
        $this->logoutModel = new Logout();
        // $this->apiResponseModel = new Api_response();
    }
    
    /**
     * Login user
     */
    public function index()
    {
        $this->requested_by = $this->request->getVar('requester');
        // if (($response = $this->_api_verification('logouts', 'index')) !== true) {
        //     return $response;
        // }

        $where = ['id' => $this->requested_by, 'is_deleted' => 0];

        $user_query = $this->userModel->where('is_deleted', 0)->where('id', $this->requested_by)->findAll();
        if (!$user = $user_query?$user_query[0]:false) {
            $response = $this->failNotFound('User not found.');
        } elseif (!$user = $this->_attempt_logout($user)) {
            $response = $this->fail('Logout failed. Please try again.');
        } else {
            $response = ['response' => 'Logout successful.'];
            $response = $this->respond($response);
        }

        // $this->apiResponseModel->record_response($this->api_log_id, $response);
        return $response;
    }

    protected function _attempt_logout($user)
    {
        $login_id = $this->request->getVar('login_id');

        if(!$user) {
            return false;
        }

        $values = [
            'user_id' => $user['id'],
            'login_id' => $login_id,
            'logged_out_on' => date('Y-m-d H:i:s')
        ];

        $user_token = ['token' => null];

        if (!$this->userModel->update($user['id'], $user_token)
            // !$this->logoutModel->insert($values)
        ) {
            return false;
        }
        return true;
    }
}
