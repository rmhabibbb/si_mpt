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
		$this->load->model('Kategori_m');
		$this->load->model('Barang_m');
		$this->load->model('Supplier_m');
		$this->load->model('Transaksi_m');
		$this->load->model('DetailTransaksi_m');
		date_default_timezone_set("Asia/Jakarta");


		$this->data['profil'] = $this->Akun_m->get_row(['username' => $this->data['username']]);
	}
	public function index()
	{
		redirect('kasir/transaksi');
	}

	// TRANSAKSI
	public function transaksi()
	{
		$this->data['title'] 	= 'Transaksi Barang';
		$this->data['index'] 	= 2;
		$this->data['link'] 	= 'transaksi';

		$this->data['list_kategori']   	= $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'kasir/transaksi/index';
		$this->load->view('kasir/template/layout', $this->data);
	}

	public function getDataBarangTransaksi()
	{
		$x = $this->input->get('x');
		$list = $this->Barang_m->get_datatables($x);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();

			if ($field->foto == NULL || $field->foto == '') {
				$row[] = '<span style="text-align:center;">
							<img alt="Image placeholder" src="' . base_url('assets/default.png') . '">
						</span> ';
			} else {

				$row[] = '<span style="text-align:center"> 
							<img alt="Image placeholder" src="' . base_url('assets/barang')  . '/' . $field->foto . '">
						</span> ';
			}

			$row[] = $field->nama_barang . '<br>(' . $field->nama_kategori . ')';
			$row[] = $field->jenis . '<br> ' . $field->ukuran . '<br> Stok : ' . $field->stok;

			$row[] = '<div class="w-100 text-center" ><button data-bs-target="#modal_detail_barang" data-bs-toggle="modal" id="view-modal_detail_barang"  class="btn btn-sm btn-success"  data-id="' . $field->id_barang . '"><span class="fa fa-eye  btn-aksi"></span></button></div>';

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

	public function get_barang_by_id()
	{
		$id 	= $this->input->post('id');
		$data['barang'] 	= $this->Barang_m->view_barang_by_id(['id_barang' => $id]);

		echo json_encode($data);
	}

	public function send_to_cart()
	{
		$id 	= $this->input->post('id');
		$qty 	= $this->input->post('qty');

		$barang = $this->Barang_m->get_row(['id_barang' => $id]);

		if ($qty > $barang->stok) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> 'Stok barang tidak cukup!',
			];
		} else {
			$cart = $this->Transaksi_m->get_row(['username' => $this->data['profil']->username, 'status' => 0]);
			if (isset($cart)) {
				$cek = $this->DetailTransaksi_m->get_row(['kd_transaksi' => $cart->kd_transaksi, 'id_barang' => $id]);

				if (isset($cek)) {
					$data_cart = [
						'kd_transaksi' => $cart->kd_transaksi,
						'id_barang' => $id,
						'qty' => $cek->qty + $qty,
						'harga' => $barang->harga,
						'sub_total' => $cek->sub_total + ($barang->harga * $qty),
						'is_return' => 0
					];
					if ($this->DetailTransaksi_m->update($cek->id, $data_cart)) {
						$this->Barang_m->update($id, ['stok' => $barang->stok - $qty]);
						$data = [
							'status' 		=> 'success',
							'icon' 		=> 'success',
							'message' 		=> 'barang berhasil ditambah ke keranjang.',
						];
					} else {
						$data = [
							'status' 		=> 'warning',
							'icon' 		=> 'warning',
							'message' 		=> 'Gagal, coba lagi!',
						];
					}
				} else {
					$data_cart = [
						'kd_transaksi' => $cart->kd_transaksi,
						'id_barang' => $id,
						'qty' => $qty,
						'harga' => $barang->harga,
						'sub_total' => $barang->harga * $qty,
						'is_return' => 0
					];
					if ($this->DetailTransaksi_m->insert($data_cart)) {
						$this->Barang_m->update($id, ['stok' => $barang->stok - $qty]);
						$data = [
							'status' 		=> 'success',
							'icon' 		=> 'success',
							'message' 		=> 'barang berhasil ditambah ke keranjang.',
						];
					} else {
						$data = [
							'status' 		=> 'warning',
							'icon' 		=> 'warning',
							'message' 		=> 'Gagal, coba lagi!',
						];
					}
				}
			} else {
				$tb = 'TB-' . date('YmdHis');
				$data_trans = [
					'kd_transaksi' => $tb,
					'status' => 0,
					'username' => $this->data['profil']->username
				];

				if ($this->Transaksi_m->insert($data_trans)) {
					$cek = $this->DetailTransaksi_m->get_row(['kd_transaksi' => $tb, 'id_barang' => $id]);

					if (isset($cek)) {
						$data_cart = [
							'kd_transaksi' => $tb,
							'id_barang' => $id,
							'qty' => $cek->qty + $qty,
							'harga' => $barang->harga,
							'sub_total' => $cek->sub_total + ($barang->harga * $qty),
							'is_return' => 0
						];
						if ($this->DetailTransaksi_m->update($cek->id, $data_cart)) {
							$this->Barang_m->update($id, ['stok' => $barang->stok - $qty]);
							$data = [
								'status' 		=> 'success',
								'icon' 		=> 'success',
								'message' 		=> 'barang berhasil ditambah ke keranjang.',
							];
						} else {
							$data = [
								'status' 		=> 'warning',
								'icon' 		=> 'warning',
								'message' 		=> 'Gagal, coba lagi!',
							];
						}
					} else {
						$data_cart = [
							'kd_transaksi' => $tb,
							'id_barang' => $id,
							'qty' => $qty,
							'harga' => $barang->harga,
							'sub_total' => $barang->harga * $qty,
							'is_return' => 0
						];
						if ($this->DetailTransaksi_m->insert($data_cart)) {
							$this->Barang_m->update($id, ['stok' => $barang->stok - $qty]);
							$data = [
								'status' 		=> 'success',
								'icon' 		=> 'success',
								'message' 		=> 'barang berhasil ditambah ke keranjang.',
							];
						} else {
							$data = [
								'status' 		=> 'warning',
								'icon' 		=> 'warning',
								'message' 		=> 'Gagal, coba lagi!',
							];
						}
					}
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

	public function send_checkout()
	{
		$id = $this->POST('kd_transaksi');
		$data = [
			'nama_customer' => addslashes($this->input->post('nama_customer', true)),
			'kontak_customer' => addslashes($this->input->post('kontak_customer', true)),
			'alamat_customer' => addslashes($this->input->post('alamat_customer', true)),
			'total_bayar' => addslashes($this->input->post('total_bayar', true)),
			'tgl_transaksi' => date('Y-m-d H:i:s'),
			'status' => 1
		];

		if ($this->Transaksi_m->update($id, $data)) {
			$data = [
				'status' 		=> 'success',
				'icon' 		=> 'success',
				'message' 		=> 'Transaksi berhasil dicheckout!',
			];
		} else {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> 'Gagal, coba lagi!',
			];
		}
		echo json_encode($data);
	}

	public function batal_transaksi()
	{
		$kd 	= $this->input->post('kd');

		$transaksi = $this->Transaksi_m->get_row(['kd_transaksi' => $kd]);
		$detail = $this->DetailTransaksi_m->get(['kd_transaksi' => $kd]);

		foreach ($detail as $val) {
			$stok = $this->Barang_m->get_row(['id_barang' => $val->id_barang])->stok;
			$this->Barang_m->update($val->id_barang, ['stok' => $stok + $val->qty]);
			$this->DetailTransaksi_m->delete($val->id);
		}

		if ($this->Transaksi_m->delete($kd)) {
			$data = [
				'status' 		=> 'success',
				'icon' 		=> 'success',
				'message' 		=> 'Transaksi berhasil dibatalkan',
			];
		} else {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> 'Gagal, coba lagi',
			];
		}

		echo json_encode($data);
	}

	public function kurang_detail()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);
		$stok = $this->Barang_m->get_row(['id_barang' => $detail->id_barang])->stok;

		if (($detail->qty - 1) == 0) {
			$this->DetailTransaksi_m->delete($id);
			$this->Barang_m->update($detail->id_barang, ['stok' => $stok + $detail->qty]);
		} else {
			$this->DetailTransaksi_m->update($id, ['qty' => $detail->qty - 1, 'sub_total' => $detail->sub_total - $detail->harga]);

			$this->Barang_m->update($detail->id_barang, ['stok' => $stok  + 1]);
		}

		$data = [
			'status' 		=> 'success',
			'icon' 		=> 'success',
			'message' 		=> 'Berhasil',
		];
		echo json_encode($data);
	}

	public function tambah_detail()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);
		$stok = $this->Barang_m->get_row(['id_barang' => $detail->id_barang])->stok;

		if ($stok == 0) {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> 'Stok kurang',
			];
		} else {

			$this->Barang_m->update($detail->id_barang, ['stok' => $stok  - 1]);
			$this->DetailTransaksi_m->update($id, ['qty' => $detail->qty + 1, 'sub_total' => $detail->sub_total + $detail->harga]);
			$data = [
				'status' 		=> 'success',
				'icon' 		=> 'success',
				'message' 		=> 'Berhasil',
			];
		}


		echo json_encode($data);
	}

	public function batal_detail()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);
		$stok = $this->Barang_m->get_row(['id_barang' => $detail->id_barang])->stok;

		$this->DetailTransaksi_m->delete($id);

		$this->Barang_m->update($detail->id_barang, ['stok' => $stok  + $detail->qty]);
		$data = [
			'status' 		=> 'success',
			'icon' 		=> 'success',
			'message' 		=> 'berhasil',
		];

		echo json_encode($data);
	}

	public function load_cart()
	{
		$data['cart'] = $this->Transaksi_m->get_row(['username' => $this->data['profil']->username, 'status' => 0]);
		if (isset($data['cart'])) {
			$data['detail_cart'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $data['cart']->kd_transaksi]);
			$data['code'] = 200;
			echo json_encode($data);
		} else {
			$data['code'] = 400;
			echo json_encode($data);
		}
	}

	// TRANSAKSI

	// RETUR TRANSAKSI

	public function retur()
	{
		$this->data['title'] 	= 'Return Transaksi';
		$this->data['index'] 	= 9;
		$this->data['link'] 	= 'return';

		$this->data['list_kategori']   	= $this->Kategori_m->get();
		$this->data['content'] 	= 'kasir/transaksi/retur_transaksi';
		$this->load->view('kasir/template/layout', $this->data);
	}

	public function getDataTransaksi()
	{
		$list = $this->Transaksi_m->get_datatables("", "", 1);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();


			$row[] = $field->kd_transaksi;
			$row[] = ($field->nama_customer != NULL) ? $field->nama_customer : '-';
			$row[] = number_format($field->total_bayar, 2, '.', ',');
			$row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

			$row[] = '<a href="' . base_url('kasir/detail_transaksi/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->Transaksi_m->count_all(),
			"recordsFiltered" 	=> $this->Transaksi_m->count_filtered(),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function getDetailTransaksi()
	{
		$kd = $this->POST('kd');
		$list = $this->DetailTransaksi_m->get_datatables($kd);
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $field) {

			$row = array();


			$row[] = $i++;
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
			$row[] = number_format($field->harga, 2, '.', ',');
			$row[] = $field->qty;
			$row[] = ($field->qty_return == 0) ? '0' : $field->qty_return;
			$row[] = number_format($field->sub_total, 2, '.', ',');

			if ($field->qty != $field->qty_return) {
				$row[] = '<div class="w-100 text-center" ><button data-bs-target="#modal_retur_barang" data-bs-toggle="modal" id="view-modal_retur_barang"  class="btn btn-sm btn-warning" data-harga="' . $field->harga . '"   data-sub="' . $field->sub_total . '" data-qty-return="' . $field->qty_return . '"  data-qty="' . $field->qty . '" data-barang="' . $field->id_barang . '" data-id="' . $field->id . '">Retur</button></div>';
			} else {
				$row[] = '-';
			}

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->DetailTransaksi_m->count_all($kd),
			"recordsFiltered" 	=> $this->DetailTransaksi_m->count_filtered($kd),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function detail_transaksi()
	{
		$id =  $this->uri->segment(3);
		if ($this->Transaksi_m->get_num_row(['kd_transaksi' => $id]) == 0) {
			$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
			redirect('kasir/retur');
			exit;
		}

		$this->data['trans'] = $this->Transaksi_m->get_row(['kd_transaksi' => $id]);


		$this->data['title'] 	= 'Detail Transaksi';
		$this->data['index'] 	= 9;
		$this->data['link'] 	= 'return';

		$this->data['content'] 	= 'kasir/transaksi/detail_transaksi';
		$this->load->view('kasir/template/layout', $this->data);
	}

	public function send_return()
	{
		$id = $this->POST('id');
		$kd = $this->POST('kd_transaksi');
		$qty_return = $this->POST('qty_return');

		$trans = $this->Transaksi_m->get_row(['kd_transaksi' => $kd]);

		$dtrans = $this->DetailTransaksi_m->get_row(['id' => $id]);

		$stok = $this->Barang_m->get_row(['id_barang' => $dtrans->id_barang])->stok;

		$subtotal = $dtrans->sub_total - ($qty_return * $dtrans->harga);

		if ($this->DetailTransaksi_m->update($id, ['sub_total' => $subtotal, 'qty_return' => $dtrans->qty_return + $qty_return])) {
			$total_bayar = $this->DetailTransaksi_m->get_totalbayar($kd);

			if ($this->Transaksi_m->update($kd, ['total_bayar' => $total_bayar])) {
				if ($this->Barang_m->update($dtrans->id_barang, ['stok' => $stok + $qty_return])) {
					$data = [
						'status' 		=> 'success',
						'icon' 		=> 'success',
						'message' 		=> 'Barang berhasil di retur!',
						'total_bayar' => $total_bayar
					];
				} else {
					$data = [
						'status' 		=> 'warning',
						'icon' 		=> 'warning',
						'message' 		=> 'Gagal, coba lagi!',
					];
				}
			} else {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'Gagal, coba lagi!',
				];
			}
		} else {
			$data = [
				'status' 		=> 'warning',
				'icon' 		=> 'warning',
				'message' 		=> 'Gagal, coba lagi!',
			];
		}

		echo json_encode($data);
	}
	// RETUR TRANSAKSI

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