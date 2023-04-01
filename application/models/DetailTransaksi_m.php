<?php
class DetailTransaksi_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->data['primary_key'] = 'id';
        $this->data['table_name'] = 'tbl_detail_transaksi';
    }

    var $column_order = array(
        null,
        'id',
        'nama_barang',
        'harga',
        'qty',
        'qty_return',
        'sub_total',
    ); //field yang ada di table user
    var $column_search = array('id', 'nama_barang', 'qty', 'sub_total', 'qty_return', 'harga'); //field yang diizin untuk pencarian 
    var $order = array('id' => 'DESC'); // default order 

    private function _get_datatables_query($kd)
    {

        $this->db->select('a.*, b.nama_barang, b.foto');
        $this->db->join('tbl_barang as b', 'a.id_barang=b.id_barang', 'left');
        $this->db->where(['kd_transaksi' => $kd]);
        $this->db->from('tbl_detail_transaksi as a');

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



        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($kd)
    {
        $this->_get_datatables_query($kd);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($kd)
    {
        $this->_get_datatables_query($kd);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($kd)
    {

        $this->db->select('a.*, b.nama_barang, b.foto');
        $this->db->join('tbl_barang as b', 'a.id_barang=b.id_barang', 'left');
        $this->db->where(['kd_transaksi' => $kd]);
        $this->db->from('tbl_detail_transaksi as a');
        return $this->db->count_all_results();
    }

    public function view_transaksi_detail($cond)
    {
        $this->db->where($cond);
        $this->db->select('a.*, b.nama_barang')->join('tbl_barang b', 'a.id_barang=b.id_barang', 'left');
        $query = $this->db->get('tbl_detail_transaksi as a');

        return $query->result();
    }

    public function get_totalbayar($kd)
    {
        $this->db->select('sum(sub_total) as total_bayar');
        $this->db->where(['kd_transaksi' => $kd]);
        $query = $this->db->get('tbl_detail_transaksi');

        return $query->row()->total_bayar;
    }
}
