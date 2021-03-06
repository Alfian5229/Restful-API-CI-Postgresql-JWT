<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH .'/libraries/JWT.php'; // Include the JWT.php
use \Firebase\JWT\JWT; //namespace in jwt
use \Firebase\JWT\SignatureInvalidException;

class UsersController extends CI_Controller {

	private $secret = "codeigniterpostgresql";

	//calling model user
	public function __construct(){
        parent::__construct();
		$this->load->model('user');
		
		// Allowing CORS
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
	}

	//dynamic response
	public function response($data, $status = 200){
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

	//api for register
	public function register(){
		return $this->response($this->user->save());
	}

	//api for get all users
	public function all_users() {
        return $this->response($this->user->get_all());
	}

	//api for get 1 user by id
	public function detail_user($id) {
        return $this->response($this->user->get_all($id));
	}

	public function login() {
		//if login is invalid
        if (!$this->user->is_valid()) {
            return $this->response([
                'success'   => false,
                'message'   => 'Password or Email is wrong'
			], 401);
		}
		//if login is valid
		else {

			$email = $this->input->post('email');
			$user = $this->user->get_info($email);

			$date = new DateTime();
			$payload['id']      = $user[0]->id;
			$payload['email']   = $user[0]->email;
			$payload['iat']     = $date->getTimestamp();
			$payload['exp']     = $date->getTimestamp() + 60*60*2;
					
			// Encode data
			$output= JWT::encode($payload, $this->secret);
			return $this->response([
                'success'   => true,
				'message'   => 'Password or Email is correct',
				'token'		=> $output
			]);
			
		}
	}
	
	public function check_token() {
		$jwt = $this->input->get_request_header('Authorization');
		try {
			//decode token with HS256 method
			$decode = JWT::decode($jwt, $this->secret, array('HS256'));
			return $decode->id;
		} catch(\Exception $e) {
			return $this->response([
                'success'   => false,
				'message'   => 'invalid token',
				'id'		=> 0
            ]);
		}
	}

	public function update($id) {
		parse_str(file_get_contents("php://input"),$data);
        if ($this->protected_method($id)) {
			return $this->response($this->user->update($id, $data));
		}
    }

	public function delete($id) {
        if ($this->protected_method($id)) {
            return $this->response($this->user->delete($id));
        }
    }

	public function protected_method($id) {
		if ($id == $this->check_token()) { // Check the $id match or not with the decode->id
			return true;
		} else {
			return $this->response([
				'success'   => false,
				'message'   => "User is different"
			]);
		}
	}
}
