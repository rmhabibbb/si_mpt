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
		$this->load->model('Supplier_m');

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

		$this->data['list_kategori']   	= $this->Kategori_m->get();
		$this->data['content'] 	= 'admin/master/barang_index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function getDataBarang()
	{
		$x = $this->input->get('x');
		$list = $this->Barang_m->get_datatables($x);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();
			$row[] = '<span style="text-align:center; vertical-align:middle">' . $field->id_barang . '</span>';


			if ($field->foto == NULL || $field->foto == '') {
				$row[] = '<span style="text-align:center;">
							<img alt="Image placeholder" src="' . base_url('assets/default.png') . '">
						</span> ';
			} else {

				$row[] = '<span style="text-align:center"> 
							<img alt="Image placeholder" src="' . base_url('assets/barang')  . '/' . $field->foto . '">
						</span> ';
			}

			$row[] = $field->nama_barang;
			$row[] = $field->nama_kategori;
			$row[] = '<span class="rupiah">Rp. ' . number_format($field->harga, 0, ',', '.') . '</span>';
			$row[] = $field->stok;

			$row[] = '<a href="' . base_url('admin/barang_edit/') . $field->id_barang . '" class="btn btn-success me-2"><i class="fas fa-edit"></i></a>
			<a href="javascript:" class="btn btn-danger hapus-barang" onClick="hapusBarang(' . $field->id_barang . ')" data-id="' . $field->id_barang . '"><i class="fas fa-trash"></i></a>';

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->Barang_m->count_all($x),
			"recordsFiltered" 	=> $this->Barang_m->count_filtered($x),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
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
		$this->form_validation->set_rules('stok', 'Stok Barang', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['title'] 	= 'Form Tambah Barang';
			$this->data['index'] 	= 5.3;
			$this->data['link'] 	= 'master_barang';

			$this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
			$this->data['content'] 	= 'admin/master/barang_form';
			$this->load->view('admin/template/layout', $this->data);
		} else {

			if ($_FILES['foto']['name'] !== '') {
				$id_foto = rand(1, 9);
				for ($j = 1; $j <= 4; $j++) {
					$id_foto .= rand(0, 9);
				}

				if ($this->upload($id_foto, 'barang', 'foto')) {
					$foto = $id_foto . '.jpg';
				} else {
					$this->session->set_flashdata('warning', 'Gagal upload foto, coba lagi!');
					redirect('admin/barang_form');
					exit;
				}
			} else {
				$foto = NULL;
			}

			$data = [
				'nama_barang' => addslashes($this->input->post('nama_barang', true)),
				'id_kategori' => addslashes($this->input->post('id_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'harga' => addslashes($this->input->post('harga', true)),
				'foto' => $foto,
				'stok' => addslashes($this->input->post('stok', true)),
				'ukuran' => addslashes($this->input->post('ukuran', true)),
				'jenis' => addslashes($this->input->post('jenis', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Barang_m->insert($data)) {
				$this->session->set_flashdata('success', 'Barang berhasil ditambah!');
				redirect('admin/barang');
				exit;
			} else {
				$this->session->set_flashdata('warning', 'Gagal, coba lagi!');
				redirect('admin/barang_form');
				exit;
			}
		}
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

		$this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['barang'] = $this->Barang_m->get_row(['id_barang' => $id]);

		$this->data['title'] 	= 'Form Edit Barang';
		$this->data['index'] 	= 5.3;
		$this->data['link'] 	= 'master_barang';

		$this->data['content'] 	= 'admin/master/barang_edit';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function barang_update()
	{
		$id = $this->POST('id_barang');
		$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
		$this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
		$this->form_validation->set_rules('harga', 'Harga Harga', 'required');
		$this->form_validation->set_rules('stok', 'Stok Barang', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
			$this->data['barang'] = $this->Barang_m->get_row(['id_barang' => $id]);

			$this->data['title'] 	= 'Form Edit Barang';
			$this->data['index'] 	= 5.3;
			$this->data['link'] 	= 'master_barang';

			$this->data['content'] 	= 'admin/master/barang_edit';
			$this->load->view('admin/template/layout', $this->data);
		} else {

			$path = $this->POST('path');

			if ($_FILES['foto']['name'] !== '') {
				$id_foto = rand(1, 9);
				for ($j = 1; $j <= 4; $j++) {
					$id_foto .= rand(0, 9);
				}
				if ($this->upload($id_foto, 'barang', 'foto')) {
					@unlink(realpath(APPPATH . '../assets/barang/' . $path));
					$foto = $id_foto . '.jpg';
				} else {
					$this->session->set_flashdata('warning', 'Gagal upload foto, coba lagi!');
					redirect('admin/barang_edit/' . $id);
					exit;
				}
			} else {
				$foto = $path;
			}

			$data = [
				'nama_barang' => addslashes($this->input->post('nama_barang', true)),
				'id_kategori' => addslashes($this->input->post('id_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'harga' => addslashes($this->input->post('harga', true)),
				'foto' => $foto,
				'stok' => addslashes($this->input->post('stok', true)),
				'ukuran' => addslashes($this->input->post('ukuran', true)),
				'jenis' => addslashes($this->input->post('jenis', true)),
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($this->Barang_m->update($id, $data)) {
				$this->session->set_flashdata('success', 'Data barang berhasil diedit!');
				redirect('admin/barang_edit/' . $id);
				exit;
			} else {
				$this->session->set_flashdata('warning', 'Gagal, coba lagi!');
				redirect('admin/barang_edit/' . $id);
				exit;
			}
		}
	}

	public function barang_delete()
	{
		$id = $this->input->post('id');
		$this->Barang_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

		redirect('admin/barang');
	}

	// MASTER BARANG

	// MASTER SUPPLIER

	public function supplier()
	{
		$this->data['title'] 	= 'Master Supplier';
		$this->data['index'] 	= 5.4;
		$this->data['link'] 	= 'master_supplier';
		$this->data['list_supplier']   	= $this->Supplier_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/supplier_index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function supplier_form()
	{
		$this->data['title'] 	= 'Form Tambah Supplier';
		$this->data['index'] 	= 5.4;
		$this->data['link'] 	= 'master_supplier';

		$this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/master/supplier_form';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function supplier_store()
	{
		$this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
		$this->form_validation->set_rules('kontak', 'Kontak', 'required');
		$this->form_validation->set_rules('alamat', 'Alamat', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {
			$data_supplier = [
				'nama_supplier' => addslashes($this->input->post('nama_supplier', true)),
				'kontak' => addslashes($this->input->post('kontak', true)),
				'alamat' => addslashes($this->input->post('alamat', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Supplier_m->insert($data_supplier)) {
				$data = [
					'status' 		=> 'success',
					'icon' 		=> 'success',
					'message' 		=> 'Supplier berhasil ditambah!',
				];
			} else {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		}
		echo json_encode($data);
	}

	public function supplier_edit()
	{
		$id =  $this->uri->segment(3);
		if ($this->Supplier_m->get_num_row(['id_supplier' => $id]) == 0) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/supplier');
			exit;
		} elseif ($this->Supplier_m->get_row(['id_supplier' => $id])->deleted_at != NULL) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('admin/supplier');
			exit;
		}

		$this->data['supplier'] = $this->Supplier_m->get_row(['id_supplier' => $id]);

		$this->data['title'] 	= 'Form Edit Supplier';
		$this->data['index'] 	= 5.4;
		$this->data['link'] 	= 'master_supplier';

		$this->data['content'] 	= 'admin/master/supplier_edit';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function supplier_update()
	{
		$this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
		$this->form_validation->set_rules('kontak', 'Kontak', 'required');
		$this->form_validation->set_rules('alamat', 'Alamat', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {
			$id = $this->POST('id_supplier');
			$data_supplier = [
				'nama_supplier' => addslashes($this->input->post('nama_supplier', true)),
				'kontak' => addslashes($this->input->post('kontak', true)),
				'alamat' => addslashes($this->input->post('alamat', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Supplier_m->update($id, $data_supplier)) {
				$data = [
					'status' 		=> 'success',
					'icon' 		=> 'success',
					'message' 		=> 'Supplier berhasil diedit!',
				];
			} else {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		}
		echo json_encode($data);
	}

	public function supplier_delete()
	{
		$id = $this->input->post('id');
		$this->Supplier_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

		redirect('admin/supplier');
	}

	// MASTER BARANG



















	// PROFILE

	public function profil()
	{

		$this->data['title'] 	= 'Profile';
		$this->data['link'] 	= 'profile';
		$this->data['index'] = 6;
		$this->data['content'] = 'admin/profile';
		$this->load->view('admin/template/layout', $this->data);
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