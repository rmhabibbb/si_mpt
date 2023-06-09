<?php

class Login extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->data['username'] = $this->session->userdata('username');
    $this->data['id_role']  = $this->session->userdata('id_role');
    if (isset($this->data['username'], $this->data['id_role'])) {
      switch ($this->data['id_role']) {
        case 1:
          redirect('admin');
          break;
        case 2:
          redirect('kasir');
          break;
        case 3:
          redirect('pemilik');
          break;
      }
      exit;
    }
    $this->load->model('Akun_m');
  }

  public function cek()
  {
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $cek = $this->Akun_m->cek_login($username, $password);
    if ($cek == 0) {
      $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable text-white">username tidak terdaftar!</div>');
      redirect('login');
      exit;
    } else if ($cek == 1) {
      setcookie('username_temp', $username, time() + 5, "/");
      $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable text-white">Password Salah!</div>');
      redirect('login');
      exit;
    } else if ($cek == 2) {
      $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable text-white">Akun anda tidak aktif!</div>');
      redirect('login');
      exit;
    }
    redirect('login');
  }

  public function index()
  {
    $this->load->view('sign-in');
  }
}
