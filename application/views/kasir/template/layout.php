<?php
$data = [
  'index' => $index
];
$this->load->view('kasir/template/header', $data);
$this->load->view('kasir/template/sidebar', $data);
$this->load->view('kasir/template/navbar');
$this->load->view($content);
$this->load->view('kasir/template/footer');
