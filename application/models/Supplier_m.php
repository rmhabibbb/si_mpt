<?php
class Supplier_m extends MY_Model
{
  function __construct()
  {
    parent::__construct();
    $this->data['primary_key'] = 'id_supplier';
    $this->data['table_name'] = 'tbl_supplier';
  }
}
