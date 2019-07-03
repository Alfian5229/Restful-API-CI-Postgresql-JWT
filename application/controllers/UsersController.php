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
	}

	//dynamic response
	public function response($data){
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
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
            ]);
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
			var_dump($decode);
		} catch(\Exception $e) {
			return $this->response([
                'success'   => false,
                'message'   => 'invalid token'
            ]);
		}
	}
}
