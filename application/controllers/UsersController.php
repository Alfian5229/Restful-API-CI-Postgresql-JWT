<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersController extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('user');
	}
	
	public function register(){
		$this->user->save();
    }
}
