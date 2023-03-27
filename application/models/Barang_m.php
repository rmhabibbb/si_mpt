<?php
class Barang_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->data['primary_key'] = 'id_barang';
        $this->data['table_name'] = 'tbl_barang';
    }

    var $table = 'tbl_barang'; //nama tabel dari database
    var $column_order = array(
        null,
        'nama_barang',
        'nama_kategori',
        'harga',
        'stok',
    ); //field yang ada di table user
    var $column_search = array('nama_barang', 'nama_kategori', 'harga', 'stok'); //field yang diizin untuk pencarian 
    var $order = array('id_barang' => 'ASC'); // default order 

    private function _get_datatables_query($x)
    {
        $this->db->select('a.*, b.nama_kategori');
        $this->db->from('tbl_barang a');
        $this->db->join('tbl_kategori b', 'a.id_kategori = b.id_kategori', 'left');
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

        if (isset($x) && $x != '') {
            $this->db->where(['a.id_kategori' => $x]);
        }

        $this->db->where(['a.deleted_at' => NULL]);


        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($x)
    {
        $this->_get_datatables_query($x);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($x)
    {
        $this->_get_datatables_query($x);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($x)
    {
        if (isset($x) && $x != '') {
            $this->db->where(['a.id_kategori' => $x]);
        }

        $this->db->where(['a.deleted_at' => NULL]);
        $this->db->select('a.*, b.nama_kategori');
        $this->db->from('tbl_barang a');
        $this->db->join('tbl_kategori b', 'a.id_kategori = b.id_kategori', 'left');
        return $this->db->count_all_results();
    }
}
