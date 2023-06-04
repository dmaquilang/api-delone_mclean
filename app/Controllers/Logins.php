<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Login;
use App\Models\Api_response;

use CodeIgniter\RESTful\ResourceController;

class Logins extends ResourceController
{
    
    public function __construct()
    {
        $this->api_key = $_SERVER['HTTP_API_KEY'];
        $this->userModel = new User();
        $this->loginModel = new Login();
        // $this->apiResponseModel = new Api_response();
    }
    
    /**
     * Login user
     */
    public function index()
    {
        $this->requested_by = 0;
        // if (($response = $this->_api_verification('logins', 'index')) !== true) {
        //     return $response;
        // }

        $where = ['is_deleted' => 0];
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $user_type = $this->request->getVar('user_type');

        $user_type = $this->request->getVar('user_type') ? : "Patient";
        $user_type = ucwords($user_type);
        // $area_code = $this->request->getVar('area_code');

        // $where = ['username' => $username, 'area_code' => $area_code, 'is_deleted' => 0];
        $where = ['username' => $username, 'is_deleted' => 0];
        // $questions = $this->userModel->where('is_deleted', 0)->where('username', $username)->findAll();
        // var_dump($username);
        // var_dump($this->userModel->where('is_deleted', 0)->where('username', $username)->findAll());
        // die();
        $user_query = $this->userModel->where('is_deleted', 0)->where('username', $username)->findAll();
        if (!$user = $user_query?$user_query[0]:false) {
            $response = $this->failNotFound('User not found.');
        } elseif (!$token = $this->_attempt_login($user)) {
            $response = $this->fail('Login failed. Please try again.');
        } else {
            $response = [];
            $user['role'] = strtolower($user_type);
            $user['type'] = $user_type;
            $user["token"] = $token["token"];
            $response['user'] = $user;
            $response = $this->respond($response);
        }

        return $response;
    }

    /**
     * Verify user
     */
    public function verify()
    {
        $this->requested_by = $this->request->getVar('id');
        $where = ['is_deleted' => 0];
        $id = $this->request->getVar('id');
        $token = $this->request->getVar('token');
        $where = ['id' => $id, 'token' => $token];
        // var_dump($token,  $this->requested_by);
        // var_dump($this->userModel->where('token', $token)->where('id', $this->requested_by)->findAll());
        // die();
        if (empty($this->userModel->where('token', $token)->where('id', $this->requested_by)->findAll())) {
            $response = [];
            $response['verified'] = false;
        } else {
            $response = [];
            $response['verified'] = true;
        }
            $response = $this->respond($response);
            return $response;
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function _attempt_login($user)
    {
        $agent = $this->request->getUserAgent();
        $token = $this->generateRandomString(60);
        $id = $user["id"];

        $values = [
            'user_id' => $id,
            'ip_address' => $this->request->getServer('REMOTE_ADDR'),
            'platform' => $agent->getPlatform(),
            'user_agent' => $this->_get_user_agent($agent),
            'logged_on' => date('Y-m-d H:i:s')
        ];

        $user_token = ['token' => $token];

        if (!$this->userModel->update($id, $user_token)
            // !$this->loginModel->insert($values)
        ) {
            return false;
        }

        $user['token'] = $token;
        unset($user['password']);
        unset($user['added_by']);
        unset($user['added_on']);
        unset($user['updated_by']);
        unset($user['updated_on']);
        unset($user['is_deleted']);

        return $user;
    }

    protected function _get_user_agent($agent)
    {
        if ($agent->isBrowser()) {
            $currentAgent = $agent->getBrowser() . ' ' . $agent->getVersion();
        } elseif ($agent->isRobot()) {
            $currentAgent = $agent->getRobot();
        } elseif ($agent->isMobile()) {
            $currentAgent = $agent->getMobile();
        } else {
            $currentAgent = 'Unidentified User Agent';
        }

        return $currentAgent;
    }
}
