<?php
$data = [
  'index' => $index
];
$this->load->view('pemilik/template/header', $data);
$this->load->view('pemilik/template/sidebar', $data);
$this->load->view('pemilik/template/navbar');
$this->load->view($content);
$this->load->view('pemilik/template/footer');
