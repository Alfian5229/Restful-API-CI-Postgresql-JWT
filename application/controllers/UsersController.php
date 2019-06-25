<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH .'/libraries/JWT.php'; // Include the JWT.php
use  \Firebase\JWT\JWT; //namespace in jwt

class UsersController extends CI_Controller {

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

	public function detail_user($id) {
        return $this->response($this->user->get_all($id));
	}
}
