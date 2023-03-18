<?php
class Supply_m extends MY_Model
{
    function __construct()
    {
        parent::__construct();
        $this->data['primary_key'] = 'kd_transaksi';
        $this->data['table_name'] = 'tbl_transaksi_supply';
    }
}
