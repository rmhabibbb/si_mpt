<aside class="d-flex align-items-start flex-column  bg-white sidenav  navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 shadow-lg  " style="background:#051e34 url(<?= base_url(); ?>assets/img/background/bg-sidebar.png); background-color:#051e34 !important;background-size:cover" id="sidenav-main">
  <div class="flex-column mb-2">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="<?= base_url(); ?>assets/img/logos/logo.jpg" class="navbar-brand-img h-100 rounded-circle border border-1" alt="main_logo">
        <span class="ms-1 font-weight-bold fs-5 txt-menu-h">Lisan Collection</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="m-user">
      <i class="fas fa-times p-2 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="#">
        <img src="<?= base_url(); ?>assets/img/user.svg" class="navbar-brand-img h-100 rounded-circle border border-3 bg-white" alt="main_logo">
        <span class="ms-1 font-weight-bold txt-menu-h"><?= strtoupper($profil->nama_user); ?></span>
      </a>
    </div>

  </div>

  <div class="collapse navbar-collapse w-100  flex-fill navbar align-items-start" id="sidenav-collapse-main">
    <!-- LOOPING MENU -->
    <ul class="navbar-nav mt-2">

      <li class="nav-item">
        <a href="<?= base_url(); ?>admin/transaksi" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 2) ? 'active' : '' ?>" id="link-transaksi">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-cart text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Transaksi Barang</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= base_url(); ?>admin/retur" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 9) ? 'active' : '' ?>" id="link-return">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-bag-17  text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Return Barang</span>
        </a>
      </li>

      <li class="nav-item">
        <a href="<?= base_url(); ?>admin/supply" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 3) ? 'active' : '' ?>" id="link-supply">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-basket text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Supply Barang</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index >= 4 && $index < 5) ? 'active' : '' ?>" id="dashboard" data-bs-toggle="collapse" href="#laporan" role="button" aria-expanded="false" aria-controls="collapseExample">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="ni ni-book-bookmark text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Laporan</span>
        </a>
        <div class="collapse ms-5 me-3 mt-1 text-start <?php echo (isset($index) && ($index > 4 && $index < 5)) ? 'show' : '' ?>" id="laporan">

          <a href="<?= base_url() ?>admin/laporan_penjualan" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 4.1) ? 'active' : '' ?>
                                    " id="link-laporan_penjualan"> Penjualan </a>
          <a href="<?= base_url() ?>admin/laporan_pembelian" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 4.2) ? 'active' : '' ?>
                                    " id="link-laporan_pembelian"> Pembelian </a>
          <a href="<?= base_url() ?>admin/laporan_stok_barang" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 4.3) ? 'active' : '' ?>
                                    " id="link-laporan_stok_barang"> Stok Barang </a>

        </div>
      </li>
      <li class="nav-item">
        <a class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index >= 5 && $index < 6) ? 'active' : '' ?>" id="dashboard" data-bs-toggle="collapse" href="#master" role="button" aria-expanded="false" aria-controls="collapseExample">
          <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-database text-white text-sm opacity-10"></i>
          </div>
          <span class="nav-link-text ms-1 fw-bold txt-menu-h">Master</span>
        </a>
        <div class="collapse ms-5 me-3 mt-1 text-start <?php echo (isset($index) && $index >= 5 & $index < 6) ? 'show' : '' ?>" id="master">

          <!-- <a href="<?= base_url() ?>admin/users" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 5.1) ? 'active' : '' ?>
                                    " id="link-master_user"> User </a> -->
          <a href="<?= base_url() ?>admin/kategori" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 5.2) ? 'active' : '' ?>
                                    " id="link-master_kategori"> Kategori </a>
          <a href="<?= base_url() ?>admin/barang" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 5.3) ? 'active' : '' ?>
                                    " id="link-master_barang"> Barang </a>
          <a href="<?= base_url() ?>admin/supplier" class="nav-link btn w-100 txt-menu
                                    <?= ($index == 5.4) ? 'active' : '' ?>
                                    " id="link-master_supplier"> Supplier </a>

        </div>
      </li>
      <li class="nav-item">
        <a href="<?= base_url(); ?>admin/profil" class="btn nav-link shadow-none border-0 <?php echo (isset($index) && $index == 6) ? 'active' : '' ?>" id="link-profile">
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