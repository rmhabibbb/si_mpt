<?php
class Barang_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->data['primary_key'] = 'id_barang';
        $this->data['table_name'] = 'tbl_barang';
    }
}
