<?php

class User extends CI_Model
{
	//function for register user
	public function save(){

		//post value
        $data   = [
            'email'     => $this->input->post('email'),
            'password'  => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
		];

		//insert to database
		$result = $this->db->insert('users',$data);
        if($result){
            return [
                'id'        => $this->db->insert_id(),
                'status'    => true,
                'message'   => 'User successfully registered'
            ];
        }
    }
}
