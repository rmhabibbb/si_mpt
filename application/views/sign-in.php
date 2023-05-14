<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?= base_url(); ?>assets/img/logos/logo.png">
    <title>
        Login - Sistem Informasi Manajemen Penjualan Terdistribusi
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="<?= base_url(); ?>assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?= base_url(); ?>assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="<?= base_url(); ?>assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= base_url(); ?>assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />
    <style type="text/css">
        .bg-straight {
            background-color: #fcd200;
        }

        .bg-btn {
            background-color: #546e7a;
        }

        .bg-btn:hover {
            background-color: #263238;
        }
    </style>
</head>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 position-absolute w-100" style="background:url(<?= base_url(); ?>assets/img/background/bg-sidebar-head.png)"></div>
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-12 d-flex justify-content-center">
                            <div class="col-md-4">

                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto border shadow-lg p-3 mb-5 bg-body rounded">
                            <div class="card card-plain">
                                <div class="pb-0 text-start text-center">
                                    <h4>Sistem Informasi Lisan Collection</h4>
                                </div>
                                <div class="card-body" style="margin-top:10px">
                                    <div id="view-alert">
                                        <?= $this->session->flashdata('msg') ?>
                                    </div>
                                    <form role="form" action="<?= base_url(); ?>login/cek" method="post">
                                        <div class="mb-3">
                                            <input type="text" class="form-control form-control-lg shadow-sm" name="username" placeholder="Username" aria-label="Email">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" class="form-control form-control-lg shadow-sm" name="password" placeholder="Password" aria-label="Password">
                                        </div>
                                        <div class="text-center">
                                            <button type="SUBMIT" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0 bg-btn" id="signIn">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!--   Core JS Files   -->
    <script src="<?= base_url(); ?>assets/js/core/popper.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/core/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $('#signIn').click(function() {
            $('#view-alert').fadeOut();
        });
        // var win = navigator.platform.indexOf('Win') > -1;
        // if (win && document.querySelector('#sidenav-scrollbar')) {
        //   var options = {
        //     damping: '0.5'
        //   }
        //   Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        // }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= base_url(); ?>assets/js/argon-dashboard.min.js?v=2.0.4"></script>
</body>

</html>