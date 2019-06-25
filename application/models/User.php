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

	//function for get all user information
	public function get_all($id = null){

		//if parameter is id, then showing user detail
		if($id != null) {
            $query  = $this->db->get_where('users', array('id' => $id));
			return $query->result();
		}
		//showing all user detail
		else {
			$query = $this->db->get('users');
			return $query->result();
		}
	}
}
