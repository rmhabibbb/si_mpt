<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['username'] = $this->session->userdata('username');
		$this->data['id_role']  = $this->session->userdata('id_role');
		if (!$this->data['username'] || ($this->data['id_role'] != 1)) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable">Anda harus login dulu</div>');
			redirect('login');
			exit;
		}

		$this->load->model('Akun_m');
		$this->load->model('Kategori_m');
		$this->load->model('Barang_m');

		$this->data['profil'] = $this->Akun_m->get_row(['username' => $this->data['username']]);
	}
	public function index()
	{
		$this->data['index'] 	= 1;
		$this->data['title'] 	= 'Dashboard';
		$this->data['link'] 	= 'dashboard';
		$this->data['content'] 	= 'admin/index';
		$this->load->view('admin/template/layout', $this->data);
	}


	// MASTER USERS

	public function users()
	{
		$this->data['title'] 	= 'Master User';
		$this->data['index'] 	= 5.1;
		$this->data['link'] 	= 'master_user';

		$this->data['users']   	= $this->Akun_m->get(['username !=' => $this->data['profil']->username, 'deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/user_index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function user_form()
	{
		$this->data['title'] 	= 'Form Tambah User';
		$this->data['index'] 	= 5.1;
		$this->data['link'] 	= 'master_user';

		$this->data['content'] 	= 'admin/master/user_form';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function user_store()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('nama_user', 'Nama', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['title'] 	= 'Form Tambah User';
			$this->data['index'] 	= 5.1;

			$this->data['content'] 	= 'admin/master/user_form';
			$this->load->view('admin/template/layout', $this->data);
		} else {

			$username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
			if ($this->Akun_m->get_num_row(['username' => $username]) != 0) {
				$this->session->set_flashdata('warning', 'Username telah digunakan!');
				redirect('admin/user_form');
				exit;
			}

			$data = [
				'username'      => addslashes($username),
				'nama_user' => addslashes($this->input->post('nama_user', true)),
				'no_hp' => addslashes($this->input->post('no_hp', true)),
				'role' => addslashes($this->input->post('role', true)),
				'is_aktif' => addslashes($this->input->post('aktif', true)),
				'password' => md5($this->input->post('password')),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Akun_m->insert($data)) {
				$this->session->set_flashdata('success', 'User berhasil ditambah!');
				redirect('admin/users');
			} else {
				$this->session->set_flashdata('warning', 'Gagal, coba lagi!');
				redirect('admin/user_form');
			}
		}
	}

	public function user_edit()
	{
		$id =  $this->uri->segment(3);
		if ($this->Akun_m->get_num_row(['username' => $id]) == 0) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/users');
			exit;
		} elseif ($this->Akun_m->get_row(['username' => $id])->deleted_at != NULL) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/users');
			exit;
		}

		$this->data['user'] = $this->Akun_m->get_row(['username' => $id]);

		$this->data['title'] 	= 'Form Edit User';
		$this->data['index'] 	= 5.1;
		$this->data['link'] 	= 'master_user';

		$this->data['content'] 	= 'admin/master/user_edit';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function user_update()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('nama_user', 'Nama', 'required');
		$this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['title'] 	= 'Form Edit User';
			$this->data['index'] 	= 5.1;
			$this->data['link'] 	= 'master_user';

			$this->data['content'] 	= 'admin/master/user_edit';
			$this->load->view('admin/template/layout', $this->data);
		} else {

			$username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
			$username_old      = preg_replace('/\s+/', '',  $this->input->post('username_old', true));
			if ($this->Akun_m->get_num_row(['username' => $username]) != 0 && ($username_old != $username)) {
				$this->session->set_flashdata('warning', 'Username telah digunakan!');
				redirect('admin/user_edit/' . $username_old);
				exit;
			}

			$data = [
				'username'      => addslashes($username),
				'nama_user' => addslashes($this->input->post('nama_user', true)),
				'no_hp' => addslashes($this->input->post('no_hp', true)),
				'role' => addslashes($this->input->post('role', true)),
				'is_aktif' => addslashes($this->input->post('aktif', true)),
				'password' => md5($this->input->post('password')),
				'updated_at' =>  date('Y-m-d H:i:s')
			];

			if ($this->Akun_m->update($username_old, $data)) {
				$this->session->set_flashdata('success', 'User berhasil diedit!');
				redirect('admin/user_edit/' . $username);
			} else {
				$this->session->set_flashdata('warning', 'Gagal, coba lagi!');
				redirect('admin/user_edit/' . $username_old);
			}
		}
	}

	public function user_update_pass()
	{
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('password_confirm', 'Password Konfirmasi', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			if ($this->input->post('password') != $this->input->post('password_confirm')) {
				$data = [
					'status' 		=> 'Warning',
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
						'status' 		=> 'Success',
						'icon' 		=> 'success',
						'message' 		=> 'Password berhasil diganti!',
					];
				} else {
					$data = [
						'status' 		=> 'Warning',
						'icon' 		=> 'warning',
						'message' 		=> 'Gagal, coba lagi!',
					];
				}
			}
		}

		echo json_encode($data);
	}

	public function user_delete()
	{
		$id = $this->input->post('id');
		$this->Akun_m->update($id, ['deleted_at' => date('Y-m-d H:i:s'), 'is_aktif' => 0]);

		redirect('admin/users');
	}

	// MASTER USERS


	// MASTER KATEGORI

	public function kategori()
	{
		$this->data['title'] 	= 'Master Kategori';
		$this->data['index'] 	= 5.2;
		$this->data['link'] 	= 'master_kategori';

		$this->data['list_kategori']   	= $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/kategori_index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function kategori_form()
	{
		$this->data['title'] 	= 'Form Tambah Kategori';
		$this->data['index'] 	= 5.2;
		$this->data['link'] 	= 'master_kategori';

		$this->data['content'] 	= 'admin/master/kategori_form';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function kategori_store()
	{
		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['title'] 	= 'Form Tambah Kategori';
			$this->data['index'] 	= 5.2;
			$this->data['link'] 	= 'master_kategori';

			$this->data['content'] 	= 'admin/master/kategori_form';
			$this->load->view('admin/template/layout', $this->data);
		} else {

			$data = [
				'nama_kategori' => addslashes($this->input->post('nama_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Kategori_m->insert($data)) {
				$this->session->set_flashdata('success', 'Kategori berhasil ditambah!');
				redirect('admin/kategori');
			} else {
				$this->session->set_flashdata('warning', 'Gagal, coba lagi!');
				redirect('admin/kategori_form');
			}
		}
	}

	public function kategori_edit()
	{
		$id =  $this->uri->segment(3);
		if ($this->Kategori_m->get_num_row(['id_kategori' => $id]) == 0) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/kategori');
			exit;
		} elseif ($this->Kategori_m->get_row(['id_kategori' => $id])->deleted_at != NULL) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/kategori');
			exit;
		}

		$this->data['kategori'] = $this->Kategori_m->get_row(['id_kategori' => $id]);

		$this->data['title'] 	= 'Form Edit Kategori';
		$this->data['index'] 	= 5.2;
		$this->data['link'] 	= 'master_kategori';

		$this->data['content'] 	= 'admin/master/kategori_edit';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function kategori_update()
	{

		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			$data = [
				'nama_kategori' => addslashes($this->input->post('nama_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($this->Kategori_m->update($this->POST('id_kategori'), $data)) {
				$data = [
					'status' 		=> 'Success',
					'icon' 		=> 'success',
					'message' 		=> 'Data berhasil diedit!',
				];
			} else {
				$data = [
					'status' 		=> 'Warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		}
		echo json_encode($data);
	}

	public function kategori_delete()
	{
		$id = $this->input->post('id');
		$this->Kategori_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

		redirect('admin/kategori');
	}

	// MASTER KATEGORI

	// MASTER BARANG

	public function barang()
	{
		$this->data['title'] 	= 'Master Barang';
		$this->data['index'] 	= 5.3;
		$this->data['link'] 	= 'master_barang';

		$this->data['list_barang']   	= $this->Barang_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/barang_index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function barang_form()
	{
		$this->data['title'] 	= 'Form Tambah Barang';
		$this->data['index'] 	= 5.3;
		$this->data['link'] 	= 'master_barang';

		$this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/barang_form';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function barang_store()
	{
		$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
		$this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
		$this->form_validation->set_rules('harga', 'Harga Harga', 'required');
		$this->form_validation->set_rules('stok', 'Foto Barang', 'required');
		$this->form_validation->set_rules('stok', 'Stok Barang', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			if ($_FILES['foto']['name'] !== '') {
				$id_foto = rand(1, 9);
				for ($j = 1; $j <= 4; $j++) {
					$id_foto .= rand(0, 9);
				}

				if ($this->upload($id_foto, 'barang', 'foto')) {
					$foto = $id_foto . '.jpg';
				} else {
					$data = [
						'status' 		=> 'Warning',
						'icon' 		=> 'warning',
						'message' 		=> 'Gagal upload foto, coba lagi!!',
					];
				}
			} else {
				$foto = NULL;
			}
			exit;

			$data = [
				'nama_barang' => addslashes($this->input->post('nama_barang', true)),
				'id_kategori' => addslashes($this->input->post('id_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'harga' => addslashes($this->input->post('harga', true)),
				'foto' => $foto,
				'stok' => addslashes($this->input->post('stok', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Barang_m->insert($data)) {
				$data = [
					'status' 		=> 'Success',
					'icon' 		=> 'success',
					'message' 		=> 'Barang berhasil ditambah!',
				];
			} else {
				$data = [
					'status' 		=> 'Warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		}
		echo json_encode($data);
	}

	public function barang_edit()
	{
		$id =  $this->uri->segment(3);
		if ($this->Barang_m->get_num_row(['id_barang' => $id]) == 0) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/barang');
			exit;
		} elseif ($this->Barang_m->get_row(['id_barang' => $id])->deleted_at != NULL) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/barang');
			exit;
		}

		$this->data['barang'] = $this->Barang_m->get_row(['id_barang' => $id]);

		$this->data['title'] 	= 'Form Edit Barang';
		$this->data['index'] 	= 5.3;
		$this->data['link'] 	= 'master_barang';

		$this->data['content'] 	= 'admin/master/barang_edit';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function barang_update()
	{

		$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			$data = [
				'nama_barang' => addslashes($this->input->post('nama_barang', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($this->Barang_m->update($this->POST('id_barang'), $data)) {
				$data = [
					'status' 		=> 'Success',
					'icon' 		=> 'success',
					'message' 		=> 'Data berhasil diedit!',
				];
			} else {
				$data = [
					'status' 		=> 'Warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		}
		echo json_encode($data);
	}

	public function barang_delete()
	{
		$id = $this->input->post('id');
		$this->Barang_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

		redirect('admin/barang');
	}

	// MASTER BARANG




















	// PROFILE

	public function profil()
	{
		$this->data['index'] = 6;
		$this->data['content'] = 'admin/index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function proses_edit_profil()
	{
		if ($this->POST('edit')) {
			if ($this->Akun_m->get_num_row(['username' => $this->POST('username')]) != 0 && $this->POST('username') != $this->POST('username_x')) {
				$this->session->set_flashdata('msg', '<div class="alert alert-warning alert-dismissable">Username telah digunakan!</div>');
				redirect('admin/profil');
				exit();
			}

			$this->Akun_m->update($this->POST('username_x'), ['username' => $this->POST('username')]);
			$user_session = [
				'username' => $this->POST('username'),
			];
			$this->session->set_userdata($user_session);


			$this->flashmsg('PROFIL BERHASIL DISIMPAN!', 'success');
			redirect('admin/profil');
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
				redirect('admin/profil');
				exit();
			}
			$data = [
				'password' => md5($this->POST('pass2'))
			];

			$this->Akun_m->update($this->data['username'], $data);

			$this->flashmsg('Password baru telah disimpan!', 'success');
			redirect('admin/profil');
			exit();
		} else {

			redirect('admin/profil');
			exit();
		}
	}
	// PROFIL

}

/* End of file Nilai.php */
/* Location: ./application/controllers/Nilai.php */