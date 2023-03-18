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
		$this->data['index'] = 7;
		$this->data['content'] = 'kasir/profil';
		$this->load->view('kasir/template/layout', $this->data);
	}

	public function proses_edit_profil()
	{
		if ($this->POST('edit')) {
			if ($this->Akun_m->get_num_row(['username' => $this->POST('username')]) != 0 && $this->POST('username') != $this->POST('username_x')) {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning alert-dismissable">Username telah digunakan!</div>');
				redirect('kasir/profil');
				exit();
			}

			$this->Akun_m->update($this->POST('username_x'), ['username' => $this->POST('username')]);
			$user_session = [
				'username' => $this->POST('username'),
			];
			$this->session->set_userdata($user_session);


			$this->flashmsg('PROFIL BERHASIL DISIMPAN!', 'success');
			redirect('kasir/profil');
			exit();
		} elseif ($this->POST('edit2')) {

			$msg = "";
			$cek = 0;

			if (md5($this->POST('pass')) != $this->data['profil']->password) {
				$msg = $msg . "Password lama salah! <br>";
				$cek = 1;
			}

			if ($this->POST('pass1') != $this->POST('pass2')) {
				$msg = $msg . "Konfirmasi Password baru tidak sama! <br>";
				$cek = 1;
			}


			if ($cek == 1) {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning alert-dismissable">' . $msg . '</div>');
				redirect('kasir/profil');
				exit();
			}
			$data = [
				'password' => md5($this->POST('pass2'))
			];

			$this->Akun_m->update($this->data['username'], $data);

			$this->flashmsg('Password baru telah disimpan!', 'success');
			redirect('kasir/profil');
			exit();
		} else {

			redirect('kasir/profil');
			exit();
		}
	}
	// PROFIL

}

/* End of file Nilai.php */
/* Location: ./application/controllers/Nilai.php */