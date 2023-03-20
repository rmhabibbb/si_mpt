<?php

class Akun_m extends MY_Model
{

	protected $data = [];
	public function __construct()
	{
		parent::__construct();
		$this->data['table_name'] 	= 'tbl_users';
		$this->data['primary_key']	= 'username';
	}

	public function cek_login($username, $password)
	{

		$this->db->where(['username' => $username]);
		$user_data = $this->db->get($this->data['table_name'])->row();
		if (isset($user_data)) {
			if ($user_data->password == md5($password)) {
				if ($user_data->is_aktif == 0) {
					return 2;
				} else {
					$user_session = [
						'username'	=> $user_data->username,
						'id_role'	=> $user_data->role
					];
					$this->session->set_userdata($user_session);
					return 3;
				}
			} else {
				return 1;
			}
		}
		return 0;
	}
}
