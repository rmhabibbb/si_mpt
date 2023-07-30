<?php
class Transaksi_m extends MY_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->data['primary_key'] = 'kd_transaksi';
		$this->data['table_name'] = 'tbl_transaksi_penjualan';
	}

	var $column_order = array(
		null,
		'kd_transaksi',
		'nama_customer',
		'total_bayar',
		'tgl_transaksi',
	); //field yang ada di table user
	var $column_search = array('kd_transaksi', 'nama_customer', 'total_bayar', 'tgl_transaksi'); //field yang diizin untuk pencarian 
	var $order = array('tgl_transaksi' => 'DESC'); // default order 

	private function _get_datatables_query($start_date, $end_date)
	{

		$i = 0;

		foreach ($this->column_search as $item) // looping awal
		{
			if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
			{

				if ($i === 0) // looping awal
				{
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if ($start_date != "" && $end_date != "") {
			if ($start_date == $end_date) {
				$this->db->where(['DATE(tgl_transaksi)' => $start_date]);
			} else {
				$this->db->where(['DATE(tgl_transaksi) >=' => $start_date]);
				$this->db->where(['DATE(tgl_transaksi) <=' => $end_date]);
			}
		}

		$this->db->where(['DATEDIFF(DATE(tgl_transaksi), CURDATE()) =' => 0]);
		$this->db->where(['status' => 1]);
		$this->db->from('tbl_transaksi_penjualan');


		if (isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($start_date = "", $end_date = "")
	{
		$this->_get_datatables_query($start_date, $end_date);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($start_date = "", $end_date = "")
	{
		$this->_get_datatables_query($start_date, $end_date);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($start_date = "", $end_date = "")
	{
		if ($start_date != "" && $end_date != "") {
			if ($start_date == $end_date) {
				$this->db->where(['DATE(tgl_transaksi)' => $start_date]);
			} else {
				$this->db->where(['DATE(tgl_transaksi) >=' => $start_date]);
				$this->db->where(['DATE(tgl_transaksi) <=' => $end_date]);
			}
		}
		$this->db->where(['status' => 1]);
		$this->db->from('tbl_transaksi_penjualan');
		return $this->db->count_all_results();
	}
}
