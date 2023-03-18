<?php

class Logout extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable">Anda berhasil logout!!</div>');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('id_role');
		redirect('login');
		exit;
	}
}
