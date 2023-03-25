<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasir extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['username'] = $this->session->userdata('username');
		$this->data['id_role']  = $this->session->userdata('id_role');
		if (!$this->data['username'] || ($this->data['id_role'] != 2)) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable">Anda harus login dulu</div>');
			redirect('login');
			exit;
		}

		$this->load->model('Akun_m');

		$this->data['profil'] = $this->Akun_m->get_row(['username' => $this->data['username']]);
	}
	public function index()
	{
		$this->data['index'] = 1;
		$this->data['title'] = 'Dashboard';
		$this->data['content'] = 'kasir/index';
		$this->load->view('kasir/template/layout', $this->data);
	}


	// PROFIL

	public function profil()
	{

		$this->data['title'] 	= 'Profile';
		$this->data['link'] 	= 'profile';
		$this->data['index'] = 6;
		$this->data['content'] = 'kasir/profile';
		$this->load->view('kasir/template/layout', $this->data);
	}

	public function profile_update()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('nama_user', 'Nama', 'required');
		$this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			$username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
			$username_old      = preg_replace('/\s+/', '',  $this->input->post('username_old', true));
			if ($this->Akun_m->get_num_row(['username' => $username]) != 0 && ($username_old != $username)) {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Username telah digunakan!',
				];
			} else {
				$data = [
					'username'      => addslashes($username),
					'nama_user' => addslashes($this->input->post('nama_user', true)),
					'no_hp' => addslashes($this->input->post('no_hp', true)),
					'updated_at' =>  date('Y-m-d H:i:s')
				];

				if ($this->Akun_m->update($username_old, $data)) {
					$user_session = [
						'username'	=> $username,
					];
					$this->session->set_userdata($user_session);
					$data = [
						'status' 		=> 'success',
						'icon' 		=> 'success',
						'message' 		=> 'Data berhasil disimpan!',
					];
				} else {
					$data = [
						'status' 		=> 'warning',
						'icon' 		=> 'warning',
						'message' 		=> 'Gagal, coba lagi!',
					];
				}
			}
		}
		echo json_encode($data);
	}

	public function profile_update_pass()
	{
		$this->form_validation->set_rules('password_old', 'Password Lama', 'required');
		$this->form_validation->set_rules('password', 'Password Baru', 'required');
		$this->form_validation->set_rules('password_confirm', 'Password Konfirmasi', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			if ($this->data['profil']->password != md5($this->POST('password_old'))) {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Password lama salah!',
				];
			} elseif ($this->input->post('password') != $this->input->post('password_confirm')) {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Password dan konfirmasi password tidak sama',
				];
			} else {
				$username      = preg_replace('/\s+/', '',  $this->input->post('id', true));
				$data = [
					'updated_at' =>  date('Y-m-d H:i:s'),
					'password' => md5($this->input->post('password')),
				];

				if ($this->Akun_m->update($username, $data)) {
					$data = [
						'status' 		=> 'success',
						'icon' 		=> 'success',
						'message' 		=> 'Password berhasil diganti!',
					];
				} else {
					$data = [
						'status' 		=> 'warning',
						'icon' 		=> 'warning',
						'message' 		=> 'Gagal, coba lagi!',
					];
				}
			}
		}

		echo json_encode($data);
	}

	// PROFIL

}

/* End of file Nilai.php */
/* Location: ./application/controllers/Nilai.php */