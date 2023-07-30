<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
		date_default_timezone_set("Asia/Jakarta");

		$this->load->model('Akun_m');
		$this->load->model('Kategori_m');
		$this->load->model('Barang_m');
		$this->load->model('Supplier_m');
		$this->load->model('Transaksi_m');
		$this->load->model('DetailTransaksi_m');
		$this->load->model('Supply_m');
		$this->load->model('LaporanStok_m');

		$this->data['profil'] = $this->Akun_m->get_row(['username' => $this->data['username']]);
	}
	public function index()
	{
		redirect('admin/transaksi');
	}

	// TRANSAKSI
	public function transaksi()
	{
		$this->data['title'] 	= 'Transaksi Barang';
		$this->data['index'] 	= 2;
		$this->data['link'] 	= 'transaksi';

		$this->data['list_kategori']   	= $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/transaksi/index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function reminder()
	{
		$x = $this->Barang_m->get(['deleted_at' => NULL, 'stok <=' => 5]);

		$data = [
			'data' => $x
		];

		echo json_encode($data);
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
		$this->data['content'] 	= 'admin/transaksi/retur_transaksi';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function getDataTransaksi()
	{
		$list = $this->Transaksi_m->get_datatables();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();


			$row[] = $field->kd_transaksi;
			$row[] = ($field->nama_customer != NULL) ? $field->nama_customer : '-';
			$row[] = number_format($field->total_bayar, 2, '.', ',');
			$row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

			$row[] = '<a href="' . base_url('admin/detail_transaksi/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

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
			redirect('admin/retur');
			exit;
		}

		$this->data['trans'] = $this->Transaksi_m->get_row(['kd_transaksi' => $id]);


		$this->data['title'] 	= 'Detail Transaksi';
		$this->data['index'] 	= 9;
		$this->data['link'] 	= 'return';

		$this->data['content'] 	= 'admin/transaksi/detail_transaksi';
		$this->load->view('admin/template/layout', $this->data);
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


	// SUPPLY BARANG
	public function supply()
	{
		$this->data['title'] 	= 'Supply Barang';
		$this->data['index'] 	= 3;
		$this->data['link'] 	= 'supply';

		$this->data['list_kategori']   	= $this->Kategori_m->get(['deleted_at' => NULL]);
		$this->data['list_supplier']   	= $this->Supplier_m->get(['deleted_at' => NULL]);
		$this->data['content'] 	= 'admin/supply/index';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function load_cart_supply()
	{
		$data['cart'] = $this->Supply_m->get_row(['username' => $this->data['profil']->username, 'status' => 0]);
		if (isset($data['cart'])) {
			$data['detail_cart'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $data['cart']->kd_transaksi]);
			$data['code'] = 200;
			echo json_encode($data);
		} else {
			$data['code'] = 400;
			echo json_encode($data);
		}
	}

	public function send_to_cart_supply()
	{
		$id 	= $this->input->post('id');
		$qty 	= $this->input->post('qty');
		$harga_supply 	= $this->input->post('harga_supply');

		$barang = $this->Barang_m->get_row(['id_barang' => $id]);

		$cart = $this->Supply_m->get_row(['username' => $this->data['profil']->username, 'status' => 0]);
		if (isset($cart)) {
			$cek = $this->DetailTransaksi_m->get_row(['kd_transaksi' => $cart->kd_transaksi, 'id_barang' => $id]);

			if (isset($cek)) {
				$data_cart = [
					'kd_transaksi' => $cart->kd_transaksi,
					'id_barang' => $id,
					'qty' => $cek->qty + $qty,
					'harga' => $harga_supply,
					'sub_total' => $cek->sub_total + ($harga_supply * $qty),
					'is_return' => 0
				];
				if ($this->DetailTransaksi_m->update($cek->id, $data_cart)) {
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
					'harga' => $harga_supply,
					'sub_total' => $harga_supply * $qty,
					'is_return' => 0
				];
				if ($this->DetailTransaksi_m->insert($data_cart)) {
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
			$tb = 'SB-' . date('YmdHis');
			$data_trans = [
				'kd_transaksi' => $tb,
				'status' => 0,
				'username' => $this->data['profil']->username
			];

			if ($this->Supply_m->insert($data_trans)) {
				$cek = $this->DetailTransaksi_m->get_row(['kd_transaksi' => $tb, 'id_barang' => $id]);

				if (isset($cek)) {
					$data_cart = [
						'kd_transaksi' => $tb,
						'id_barang' => $id,
						'qty' => $cek->qty + $qty,
						'harga' => $harga_supply,
						'sub_total' => $cek->sub_total + ($harga_supply * $qty),
						'is_return' => 0
					];
					if ($this->DetailTransaksi_m->update($cek->id, $data_cart)) {
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
						'harga' => $harga_supply,
						'sub_total' => $harga_supply * $qty,
						'is_return' => 0
					];
					if ($this->DetailTransaksi_m->insert($data_cart)) {
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

		echo json_encode($data);
	}

	public function batal_detail_supply()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);
		$this->DetailTransaksi_m->delete($id);

		$data = [
			'status' 		=> 'success',
			'icon' 		=> 'success',
			'message' 		=> 'berhasil',
		];

		echo json_encode($data);
	}

	public function tambah_detail_supply()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);

		$this->DetailTransaksi_m->update($id, ['qty' => $detail->qty + 1, 'sub_total' => $detail->sub_total + $detail->harga]);
		$data = [
			'status' 		=> 'success',
			'icon' 		=> 'success',
			'message' 		=> 'Berhasil',
		];
		echo json_encode($data);
	}

	public function batal_transaksi_supply()
	{
		$kd 	= $this->input->post('kd');

		$transaksi = $this->Supply_m->get_row(['kd_transaksi' => $kd]);
		$detail = $this->DetailTransaksi_m->get(['kd_transaksi' => $kd]);

		foreach ($detail as $val) {
			$this->DetailTransaksi_m->delete($val->id);
		}

		if ($this->Supply_m->delete($kd)) {
			$data = [
				'status' 		=> 'success',
				'icon' 		=> 'success',
				'message' 		=> 'Transaksi supply berhasil dibatalkan',
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

	public function kurang_detail_supply()
	{
		$id 	= $this->input->post('id');
		$detail = $this->DetailTransaksi_m->get_row(['id' => $id]);

		if (($detail->qty - 1) == 0) {
			$this->DetailTransaksi_m->delete($id);
		} else {
			$this->DetailTransaksi_m->update($id, ['qty' => $detail->qty - 1, 'sub_total' => $detail->sub_total - $detail->harga]);
		}

		$data = [
			'status' 		=> 'success',
			'icon' 		=> 'success',
			'message' 		=> 'Berhasil',
		];
		echo json_encode($data);
	}

	public function send_checkout_supply()
	{
		$id = $this->POST('kd_transaksi');
		$data = [
			'id_supplier' => addslashes($this->input->post('id_supplier', true)),
			'total_bayar' => addslashes($this->input->post('total_bayar', true)),
			'tgl_transaksi' => date('Y-m-d H:i:s'),
			'status' => 1
		];

		$detail = $this->DetailTransaksi_m->get(['kd_transaksi' => $id]);

		foreach ($detail as $val) {
			$barang = $this->Barang_m->get_row(['id_barang' => $val->id_barang]);
			$this->Barang_m->update($val->id_barang, ['stok' => $barang->stok + $val->qty]);
		}


		if ($this->Supply_m->update($id, $data)) {
			$data = [
				'status' 		=> 'success',
				'icon' 		=> 'success',
				'message' 		=> 'Transaksi supply barang berhasil dicheckout!',
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
	// SUPPLY BARANG


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
		$this->form_validation->set_rules('id_kategori', 'ID Kategori', 'required');
		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

		if ($this->form_validation->run() == false) {
			$this->data['title'] 	= 'Form Tambah Kategori';
			$this->data['index'] 	= 5.2;
			$this->data['link'] 	= 'master_kategori';

			$this->data['content'] 	= 'admin/master/kategori_form';
			$this->load->view('admin/template/layout', $this->data);
		} else {
			if ($this->Kategori_m->get_num_row(['id_kategori' => $this->input->post('id_kategori')]) > 0) {
				$this->session->set_flashdata('warning', 'ID Kategori telah digunakan!');
				redirect('admin/kategori_form');
				exit;
			}

			$data = [
				'id_kategori' => addslashes($this->input->post('id_kategori', true)),
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

		$idx = $this->POST('id_kategorix');
		$id = $this->POST('id_kategori');
		if ($this->Kategori_m->get_num_row(['id_kategori' => $id]) != 0 && $idx != $id) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> 'ID Kategori telah digunakan!',
			];
			echo json_encode($data);
			exit;
		}

		$this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

		if ($this->form_validation->run() == false) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> validation_errors(),
			];
		} else {

			$data = [
				'id_kategori' => $id,
				'nama_kategori' => addslashes($this->input->post('nama_kategori', true)),
				'deskripsi' => addslashes($this->input->post('deskripsi', true)),
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($this->Kategori_m->update($idx, $data)) {
				$data = [
					'status' 		=> 'success',
					'icon' 		=> 'success',
					'message' 		=> 'Data berhasil diedit!',
					'id' 		=> $id,
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
			<a href="javascript:" class="btn btn-danger hapus-barang" onClick="hapusBarang(\'' . $field->id_barang . '\')" data-id="' . $field->id_barang . '"><i class="fas fa-trash"></i></a>';

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
		$this->form_validation->set_rules('id_barang', 'ID Barang', 'required');
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
			if ($this->Barang_m->get_num_row(['id_barang' => $this->input->post('id_barang')]) > 0) {
				$this->session->set_flashdata('warning', 'ID Barang telah digunakan!');
				redirect('admin/barang_form');
				exit;
			}

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
				'id_barang' => addslashes($this->input->post('id_barang', true)),
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
		$idx = $this->POST('id_barangx');
		$id = $this->POST('id_barang');
		if ($this->Barang_m->get_num_row(['id_barang' => $id]) != 0 && $idx != $id) {
			$this->session->set_flashdata('warning', 'ID Barang telah digunakan!');

			redirect('admin/barang_edit/' . $idx);
			exit;
		}


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
				'id_barang' => addslashes($this->input->post('id_barang', true)),
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

			if ($this->Barang_m->update($idx, $data)) {
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
		$this->form_validation->set_rules('id_supplier', 'ID Supplier', 'required');
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
			if ($this->Supplier_m->get_num_row(['id_supplier' => $this->input->post('id_supplier')]) > 0) {
				$data = [
					'status' 		=> 'warning',
					'icon' 		=> 'warning',
					'message' 		=> 'ID Supplier telah digunakan!',
				];
				echo json_encode($data);
				exit;
			}

			$data_supplier = [
				'id_supplier' => addslashes($this->input->post('id_supplier', true)),
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
		$idx = $this->POST('id_supplierx');
		$id = $this->POST('id_supplier');
		if ($this->Supplier_m->get_num_row(['id_supplier' => $id]) != 0 && $idx != $id) {
			$data = [
				'status' 		=> 'Warning',
				'icon' 		=> 'warning',
				'message' 		=> 'ID Supplier telah digunakan!',
			];
			echo json_encode($data);
			exit;
		}

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
				'id_supplier' => $id,
				'nama_supplier' => addslashes($this->input->post('nama_supplier', true)),
				'kontak' => addslashes($this->input->post('kontak', true)),
				'alamat' => addslashes($this->input->post('alamat', true)),
				'created_at' => date('Y-m-d H:i:s')
			];

			if ($this->Supplier_m->update($idx, $data_supplier)) {
				$data = [
					'status' 		=> 'success',
					'icon' 		=> 'success',
					'message' 		=> 'Supplier berhasil diedit!',
					'id'	=> $id
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



	// LAPORAN PENJUALAN

	public function laporan_penjualan()
	{
		$this->data['title'] 	= 'Laporan Penjualan';
		$this->data['index'] 	= 4.1;
		$this->data['link'] 	= 'laporan_penjualan';

		$this->data['content'] 	= 'admin/laporan/penjualan';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function filterLaporan()
	{
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$list = $this->Transaksi_m->get_datatables($start_date, $end_date);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();


			$row[] = $field->kd_transaksi;
			$row[] = ($field->nama_customer != NULL) ? $field->nama_customer : '-';
			$row[] = number_format($field->total_bayar, 2, '.', ',');
			$row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

			$row[] = '<a href="' . base_url('admin/detail_laporan/t/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->Transaksi_m->count_all($start_date, $end_date),
			"recordsFiltered" 	=> $this->Transaksi_m->count_filtered($start_date, $end_date),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function download_laporan_penjualan()
	{

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan_Penjualan - SEMUA';

			$data_penjualan = $this->Transaksi_m->get(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan_Penjualan - ' . $start_date;

				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$filename         = 'Laporan_Penjualan_' . $start_date . '-' . $end_date;

				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();


		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Kode Transaksi');
		$sheet->setCellValue('C1', 'Tanggal Transaksi');
		$sheet->setCellValue('D1', 'Nama Customer');
		$sheet->setCellValue('E1', 'Kontak Customer');
		$sheet->setCellValue('F1', 'Alamat Customer');
		$sheet->setCellValue('G1', 'Total Bayar');
		$sheet->setCellValue('I1', 'Total Pendapatan Penjualan');

		$sheet->getStyle('A1')->applyFromArray($style_col);
		$sheet->getStyle('B1')->applyFromArray($style_col);
		$sheet->getStyle('C1')->applyFromArray($style_col);
		$sheet->getStyle('D1')->applyFromArray($style_col);
		$sheet->getStyle('E1')->applyFromArray($style_col);
		$sheet->getStyle('F1')->applyFromArray($style_col);
		$sheet->getStyle('G1')->applyFromArray($style_col);
		$sheet->getStyle('I1')->applyFromArray($style_col);
		$no   = 1;
		$rows = 2;
		$total = 0;
		foreach ($data_penjualan as $val) {
			$sheet->setCellValue('A' . $rows, $no);
			$sheet->setCellValue('B' . $rows, $val->kd_transaksi);
			$sheet->setCellValue('C' . $rows, date('d-m-Y', strtotime($val->tgl_transaksi)));
			$sheet->setCellValue('D' . $rows, ($val->nama_customer == "") ? '-' : $val->nama_customer);
			$sheet->setCellValue('E' . $rows, ($val->kontak_customer == "") ? '-' : $val->kontak_customer);
			$sheet->setCellValue('F' . $rows, ($val->alamat_customer == "") ? '-' : $val->alamat_customer);
			$sheet->setCellValue('G' . $rows, number_format($val->total_bayar, 2, ',', '.'));

			$sheet->getStyle('A' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('B' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('C' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('D' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('E' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('F' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('G' . $rows)->applyFromArray($style_row);

			$no++;
			$rows++;
			$total = $total + $val->total_bayar;
		}

		$sheet->setCellValue('I2', number_format($total, 2, ',', '.'));

		$sheet->getStyle('I2')->applyFromArray($style_row);

		// Set width kolom
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(40);

		$sheet->setTitle("Laporan Penjualan");

		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function detail_laporan()
	{
		if ($this->uri->segment(3) == 't') {
			$id =  $this->uri->segment(4);
			if ($this->Transaksi_m->get_num_row(['kd_transaksi' => $id]) == 0) {
				$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
				redirect('admin/laporan_penjualan');
				exit;
			}

			$this->data['trans'] = $this->Transaksi_m->get_row(['kd_transaksi' => $id]);
			$this->data['dtrans'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $id]);


			$this->data['title'] 	= 'Detail Transaksi Laporan Penjualan';
			$this->data['index'] 	= 4.1;
			$this->data['link'] 	= 'laporan_penjualan';

			$this->data['content'] 	= 'admin/laporan/detail_laporan_penjualan';
			$this->load->view('admin/template/layout', $this->data);
		} elseif ($this->uri->segment(3) == 's') {
			$id =  $this->uri->segment(4);
			if ($this->Supply_m->get_num_row(['kd_transaksi' => $id]) == 0) {
				$this->session->set_flashdata('warning', 'Data tidak ditemukan!');
				redirect('admin/laporan_penjualan');
				exit;
			}

			$this->data['trans'] = $this->Supply_m->view_detail_supply(['kd_transaksi' => $id]);
			$this->data['dtrans'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $id]);


			$this->data['title'] 	= 'Detail Transaksi Laporan Pembelian';
			$this->data['index'] 	= 4.2;
			$this->data['link'] 	= 'laporan_pembelian';

			$this->data['content'] 	= 'admin/laporan/detail_laporan_pembelian';
			$this->load->view('admin/template/layout', $this->data);
		} else {
			redirect('admin');
		}
	}

	// LAPORAN PENJUALAN

	// LAPORAN PEMBELIAN

	public function laporan_pembelian()
	{
		$this->data['title'] 	= 'Laporan Pembelian';
		$this->data['index'] 	= 4.2;
		$this->data['link'] 	= 'laporan_pembelian';

		$this->data['content'] 	= 'admin/laporan/pembelian';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function filterLaporanSupply()
	{
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$list = $this->Supply_m->get_datatables($start_date, $end_date);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {

			$row = array();


			$row[] = $field->kd_transaksi;
			$row[] = ($field->nama_supplier != NULL) ? $field->nama_supplier : '-';
			$row[] = number_format($field->total_bayar, 2, '.', ',');
			$row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

			$row[] = '<a href="' . base_url('admin/detail_laporan/s/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->Supply_m->count_all($start_date, $end_date),
			"recordsFiltered" 	=> $this->Supply_m->count_filtered($start_date, $end_date),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function download_laporan_pembelian()
	{

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan_Pembelian - SEMUA';

			$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan_Pembelian - ' . $start_date;

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$filename         = 'Laporan_Pembelian_' . $start_date . '-' . $end_date;

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();


		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Kode Transaksi');
		$sheet->setCellValue('C1', 'Tanggal Transaksi');
		$sheet->setCellValue('D1', 'Nama Supplier');
		$sheet->setCellValue('E1', 'Kontak Supplier');
		$sheet->setCellValue('F1', 'Alamat Supplier');
		$sheet->setCellValue('G1', 'Total Bayar');
		$sheet->setCellValue('I1', 'Total Harga Pembelian');

		$sheet->getStyle('A1')->applyFromArray($style_col);
		$sheet->getStyle('B1')->applyFromArray($style_col);
		$sheet->getStyle('C1')->applyFromArray($style_col);
		$sheet->getStyle('D1')->applyFromArray($style_col);
		$sheet->getStyle('E1')->applyFromArray($style_col);
		$sheet->getStyle('F1')->applyFromArray($style_col);
		$sheet->getStyle('G1')->applyFromArray($style_col);
		$sheet->getStyle('I1')->applyFromArray($style_col);
		$no   = 1;
		$rows = 2;
		$total = 0;
		foreach ($data_pembelian as $val) {
			$sheet->setCellValue('A' . $rows, $no);
			$sheet->setCellValue('B' . $rows, $val->kd_transaksi);
			$sheet->setCellValue('C' . $rows, date('d-m-Y', strtotime($val->tgl_transaksi)));
			$sheet->setCellValue('D' . $rows, ($val->nama_supplier == "") ? '-' : $val->nama_supplier);
			$sheet->setCellValue('E' . $rows, ($val->kontak_supplier == "") ? '-' : $val->kontak_supplier);
			$sheet->setCellValue('F' . $rows, ($val->alamat_supplier == "") ? '-' : $val->alamat_supplier);
			$sheet->setCellValue('G' . $rows, number_format($val->total_bayar, 2, ',', '.'));

			$sheet->getStyle('A' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('B' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('C' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('D' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('E' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('F' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('G' . $rows)->applyFromArray($style_row);

			$no++;
			$rows++;
			$total = $total + $val->total_bayar;
		}

		$sheet->setCellValue('I2', number_format($total, 2, ',', '.'));

		$sheet->getStyle('I2')->applyFromArray($style_row);

		// Set width kolom
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(40);

		$sheet->setTitle("Laporan Pembelian");

		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	// LAPORAN PEMBELIAN

	// LAPORAN PEMBELIAN

	public function laporan_stok_barang()
	{
		$this->data['title'] 	= 'Laporan Stok Barang';
		$this->data['index'] 	= 4.3;
		$this->data['link'] 	= 'laporan_stok_barang';

		$this->data['content'] 	= 'admin/laporan/stok';
		$this->load->view('admin/template/layout', $this->data);
	}

	public function filterLaporanStok()
	{
		$start_date = $this->input->get('start_date');
		$end_date = $this->input->get('end_date');
		$list = $this->LaporanStok_m->get_datatables($start_date, $end_date);

		if ($start_date == "" && $end_date == "") {
			$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}

		if ($start_date == "" && $end_date == "") {
			$data_penjualan = $this->Transaksi_m->get(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}



		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
			$n_beli = 0;
			$n_jual = 0;

			foreach ($data_pembelian as $tb) {
				$n_beli = $n_beli + $this->DetailTransaksi_m->get_sum_beli(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $field->id_barang]);
			}

			foreach ($data_penjualan as $tb) {
				$n_jual = $n_jual + $this->DetailTransaksi_m->get_sum_jual(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $field->id_barang]);
			}

			$row = array();


			$row[] = $field->id_barang;
			$row[] = ($field->nama_barang != NULL) ? $field->nama_barang : '-';
			$row[] = ($field->nama_kategori != NULL) ? $field->nama_kategori : '-';
			$row[] = ($field->ukuran != NULL) ? $field->ukuran : '-';
			$row[] = ($field->jenis != NULL) ? $field->jenis : '-';
			$row[] = ($field->deskripsi != NULL) ? $field->deskripsi : '-';
			$row[] = $n_beli;
			$row[] = $n_jual;

			$data[] = $row;
		}

		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->LaporanStok_m->count_all($start_date, $end_date),
			"recordsFiltered" 	=> $this->LaporanStok_m->count_filtered($start_date, $end_date),
			"data" 				=> $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function download_laporan_stok()
	{

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		$list_barang = $this->Barang_m->view_barang('deleted_at', 'asc', []);

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan_Stok_Barang - SEMUA';

			$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
			$data_penjualan = $this->Transaksi_m->get(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan_Stok_Barang - ' . $start_date;

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$filename         = 'Laporan_Stok_Barang_' . $start_date . '-' . $end_date;

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}


		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();


		// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
		$style_col = [
			'font' => ['bold' => true], // Set font nya jadi bold
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
			]
		];

		$sheet->setCellValue('A1', 'ID Barang');
		$sheet->setCellValue('B1', 'Nama Barang');
		$sheet->setCellValue('C1', 'Kategori');
		$sheet->setCellValue('D1', 'Ukuran');
		$sheet->setCellValue('E1', 'Jenis');
		$sheet->setCellValue('F1', 'Deskripsi');
		$sheet->setCellValue('G1', 'Jumlah Supply');
		$sheet->setCellValue('H1', 'Jumlah Terjual');

		$sheet->getStyle('A1')->applyFromArray($style_col);
		$sheet->getStyle('B1')->applyFromArray($style_col);
		$sheet->getStyle('C1')->applyFromArray($style_col);
		$sheet->getStyle('D1')->applyFromArray($style_col);
		$sheet->getStyle('E1')->applyFromArray($style_col);
		$sheet->getStyle('F1')->applyFromArray($style_col);
		$sheet->getStyle('G1')->applyFromArray($style_col);
		$sheet->getStyle('H1')->applyFromArray($style_col);
		$no   = 1;
		$rows = 2;
		$total = 0;
		foreach ($list_barang as $val) {

			$n_beli = 0;
			$n_jual = 0;

			foreach ($data_pembelian as $tb) {
				$n_beli = $n_beli + $this->DetailTransaksi_m->get_sum_beli(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
			}

			foreach ($data_penjualan as $tb) {
				$n_jual = $n_jual + $this->DetailTransaksi_m->get_sum_jual(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
			}

			$sheet->setCellValue('A' . $rows, $val->id_barang);
			$sheet->setCellValue('B' . $rows, $val->nama_barang);
			$sheet->setCellValue('C' . $rows, $val->nama_kategori);
			$sheet->setCellValue('D' . $rows, $val->ukuran);
			$sheet->setCellValue('E' . $rows, $val->jenis);
			$sheet->setCellValue('F' . $rows, $val->deskripsi);
			$sheet->setCellValue('G' . $rows, $n_beli);
			$sheet->setCellValue('H' . $rows, $n_jual);

			$sheet->getStyle('A' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('B' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('C' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('D' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('E' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('F' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('G' . $rows)->applyFromArray($style_row);
			$sheet->getStyle('H' . $rows)->applyFromArray($style_row);

			$no++;
			$rows++;
		}

		// Set width kolom
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setTitle("Laporan Stok Barang");

		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	// LAPORAN PEMBELIAN







	public function pdf_penjualan()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan_Penjualan - SEMUA';
			$this->data['periode'] = "SEMUA";
			$data_penjualan = $this->Transaksi_m->get(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan_Penjualan - ' . $start_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date));
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$filename         = 'Laporan_Penjualan_' . $start_date . '-' . $end_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}

		$this->data['data_penjualan'] = $data_penjualan;
		$this->data['start'] = $start_date;
		$this->data['end'] = $end_date;

		$mpdf = new \Mpdf\Mpdf();
		$html = $this->load->view('admin/laporan/download_penjualan', $this->data, true);
		$mpdf->WriteHTML($html);
		// $mpdf->Output(); // opens in browser
		$mpdf->Output($filename . '.pdf', 'D'); // it downloads the file into the user system, with give name
	}

	public function pdf_pembelian()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan Pembelian - SEMUA';
			$this->data['periode'] = "SEMUA";
			$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan Pembelian - ' . $start_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date));
				$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
			} else {
				$filename         = 'Laporan Pembelian ' . $start_date . '-' . $end_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));
				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}

		$this->data['data_pembelian'] = $data_pembelian;
		$this->data['start'] = $start_date;
		$this->data['end'] = $end_date;

		$mpdf = new \Mpdf\Mpdf();
		$html = $this->load->view('admin/laporan/download_pembelian', $this->data, true);
		$mpdf->WriteHTML($html);
		// $mpdf->Output(); // opens in browser
		$mpdf->Output($filename . '.pdf', 'D'); // it downloads the file into the user system, with give name
	}

	public function pdf_stok()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$list_barang = $this->Barang_m->view_barang('deleted_at', 'asc', []);

		if ($start_date == "" && $end_date == "") {
			$filename         = 'Laporan_Stok_Barang - SEMUA';
			$this->data['periode'] = "SEMUA";

			$data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
			$data_penjualan = $this->Transaksi_m->get(['status' => 1]);
		} else {
			if ($start_date == $end_date) {
				$filename         = 'Laporan_Stok_Barang - ' . $start_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date));

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
			} else {
				$filename         = 'Laporan_Stok_Barang_' . $start_date . '-' . $end_date;
				$this->data['periode'] = date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date));

				$data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
				$data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
			}
		}

		$data = [];
		foreach ($list_barang as $val) {

			$n_beli = 0;
			$n_jual = 0;

			foreach ($data_pembelian as $tb) {
				$n_beli = $n_beli + $this->DetailTransaksi_m->get_sum_beli(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
			}

			foreach ($data_penjualan as $tb) {
				$n_jual = $n_jual + $this->DetailTransaksi_m->get_sum_jual(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
			}

			$x = [
				"id_barang" => $val->id_barang,
				"nama_barang" => $val->nama_barang,
				"nama_kategori" => $val->nama_kategori,
				"ukuran" => $val->ukuran,
				"jenis" => $val->jenis,
				"n_beli" => $n_beli,
				"n_jual" => $n_jual,
			];

			array_push($data, $x);
		}

		$this->data['data'] = $data;
		$this->data['start'] = $start_date;
		$this->data['end'] = $end_date;

		$mpdf = new \Mpdf\Mpdf();
		$html = $this->load->view('admin/laporan/download_stok', $this->data, true);
		$mpdf->WriteHTML($html);
		// $mpdf->Output(); // opens in browser
		$mpdf->Output($filename . '.pdf', 'D'); // it downloads the file into the user system, with give name
	}


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