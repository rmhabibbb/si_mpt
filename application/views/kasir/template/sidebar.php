<aside class="d-flex align-items-start flex-column  bg-white sidenav  navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 shadow-lg  " style="background:#051e34 url(<?= base_url(); ?>assets/img/background/bg-sidebar.png); background-color:#051e34 !important;background-size:cover" id="sidenav-main">
  <div class="flex-column mb-2">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="<?= base_url(); ?>assets/img/logos/logo.png" class="navbar-brand-img h-100 rounded-circle border border-4" alt="main_logo">
        <span class="ms-1 font-weight-bold fs-5 txt-menu-h">SI. MPT</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="m-user">
      <i class="fas fa-times p-2 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="<?= base_url(); ?>assets/img/user.svg" class="navbar-brand-img h-100 rounded-circle border border-3 bg-white" alt="main_logo">
        <span class="ms-1 font-weight-bold txt-menu-h"><?= strtoupper($this->session->username); ?></span>
      </a>
    </div>

  </div>

  <div class="collapse navbar-collapse w-100  flex-fill navbar align-items-start" id="sidenav-collapse-main">
    <!-- LOOPING MENU -->
    <ul class="navbar-nav mt-2">
      <li class="nav-item">
        <a href="<?= base_url(); ?>kasir/transaksi" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 1) ? 'active' : '' ?>" id="link-<?= $index ?>">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-basket text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Transaksi</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= base_url(); ?>/kasir/profil" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 2) ? 'active' : '' ?>" id="link-<?= $index ?>">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-badge text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Profil</span>
        </a>
      </li>


    </ul>

  </div>


  <div class="mt-3 w-100">
    <div class="mx-3 align-items-end">
      <a href="<?= base_url(); ?>logout" class="btn btn-sm w-100 p-2 nav-link shadow-sm border-0" style="background-color: #455a64;"><span class="nav-link-text ms-1 txt-logout fw-bold text-center">LOGOUT</span></a>
    </div>
  </div>


</aside>