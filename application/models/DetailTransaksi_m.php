<?php
class DetailTransaksi_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->data['primary_key'] = 'id';
        $this->data['table_name'] = 'tbl_detail_transaksi';
    }

    public function view_transaksi_detail($cond)
    {
        $this->db->where($cond);
        $this->db->select('a.*, b.nama_barang')->join('tbl_barang b', 'a.id_barang=b.id_barang', 'left');
        $query = $this->db->get('tbl_detail_transaksi as a');

        return $query->result();
    }
}
