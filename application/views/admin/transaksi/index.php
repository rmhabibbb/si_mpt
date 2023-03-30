<link href="<?= base_url(); ?>assets/css/fancybox.min.css" rel="stylesheet">
<style>
    table td {
        text-align: center;
        vertical-align: middle;
    }

    table img {
        width: 80px;
        height: 100px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-3 mb-3">
                            <select class="form-select form-control" id="id_kategori">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($list_kategori as $kat) : ?>
                                    <option value="<?= $kat->id_kategori ?>"><?= $kat->nama_kategori ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm" id="data-table-barang" style="width:100%">
                            <thead style="background-color:#051E34;color:#fff;">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nama Barang</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-barang">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card" style="height:85vh;" style="position:fixed">
                <div class="card-body d-flex  flex-column">
                    <div class="mb-1">
                        <b>KD Transaksi : <span id="kd_trans">tidak ada data</span></b>
                    </div>
                    <hr class="bold-2">
                    <div class="row" style="font-size: 80%;  ">

                        <div class="col-5">
                            <b>Barang</b>
                        </div>
                        <div class="col-4"><b>Sub Total</b></div>
                        <div class="col-3">
                        </div>
                    </div>

                    <hr>
                    <div class="flex-fill align-items-start  " id="listCart" style="font-size: 80%; overflow-x: hidden;overflow-y: auto;  height: 130px;">

                    </div>
                    <hr>
                    <div class="align-items-end">
                        <div class="d-flex justify-content-between mx-3">
                            <span><b>Rp. </b></span>
                            <b id="total_bayar">0</b>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-danger mb-1" id="batalTransaksi" data-tb="" disabled>Batal</button>
                            <button data-bs-target="#modal_checkout_transaksi" data-bs-toggle="modal" class="btn btn-success" id="checkoutTransaksi" data-tb="" data-total="" disabled>Checkout</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/transaksi/modal/index_modal') ?>
<?php $this->load->view('admin/transaksi/modal/checkout_modal') ?>

<!-- Sweet Alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($this->session->flashdata('success')) : ?>
    <script>
        Swal.fire({
            title: "Congratulation",
            text: "<?= $this->session->flashdata('success') ?>",
            icon: "success",
            showConfirmButton: false,
            timer: 1000,
        });
    </script>
<?php endif; ?>

<?php if ($this->session->flashdata('warning')) : ?>
    <script>
        Swal.fire({
            title: "Warning",
            text: "<?= $this->session->flashdata('warning') ?>",
            icon: "warning",
            showConfirmButton: false,
            timer: 1000,
        });
    </script>
<?php endif; ?>


<!-- Datatables -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script type="text/javascript" src="<?= base_url(); ?>assets/js/fancybox.min.js"></script>

<script>
    loadCart();
    var x = $('#id_kategori').find(":selected").val();
    var dataAplikasi = $('#data-table-barang').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],

        "ajax": {
            "url": "<?= base_url(); ?>Admin/getDataBarangTransaksi",
            "type": "POST",
            "data": {
                x: x
            },

        },

        "columnDefs": [{
            "targets": [0],
            "orderable": false,
        }, {
            "targets": [2],
            "orderable": false,
        }, {
            "targets": [3],
            "orderable": false,
        }, ],

    });

    $(document).ready(function() {


        $('#id_kategori').on('change', function() {
            x = $('#id_kategori').find(":selected").val();
            dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();
        })

        $(document).on('click', '#view-modal_detail_barang', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                'url': '<?php echo base_url(); ?>Admin/get_barang_by_id',
                'type': 'POST',
                'data': {
                    id: id
                },
                'dataType': 'json',
                beforeSend: function() {
                    $('#view_modal_realisasi').hide();
                },
                success: function(respon) {

                    $('#id_barang').val(respon.barang.id_barang)
                    $('#nama_barang').val(respon.barang.nama_barang)
                    $('#nama_kategori').val(respon.barang.nama_kategori)
                    $('#jenis').val(respon.barang.jenis)
                    $('#ukuran').val(respon.barang.ukuran)
                    $('#deskripsi').val(respon.barang.deskripsi)
                    $('#stok').val(respon.barang.stok)
                    $('#harga').html(formatNumber(respon.barang.harga))
                    $('#qty').val(1);
                    $('#qty').attr('max', respon.barang.jenis);
                    $('#btnSendtoCart').attr('data-id-cart', respon.barang.id_barang);


                    if (respon.barang.foto != null) {
                        $('#foto-barang').html(`<a href="<?= base_url('assets/barang/'); ?>` + respon.barang.foto + `" data-fancybox="brg-foto" data-caption="Foto Barang">
                            <img src="<?= base_url('assets/barang/'); ?>` + respon.barang.foto + `" alt="Foto Barang" class="w-100" style="width:100%">
                        </a>`);
                    } else {
                        $('#foto-barang').html(`<a href="<?= base_url('assets/default.png'); ?>" data-fancybox="brg-foto" data-caption="Foto Barang">
                            <img src="<?= base_url('assets/default.png'); ?>" alt="Foto Barang" class="w-100" style="width:100%">
                        </a>`);
                    }


                },
                error: function() {
                    Swal.fire({
                        'title': 'Maaf',
                        'icon': 'error',
                        'text': 'Terjadi kesalahan, coba lagi!',
                    })
                }
            })
        })

        $(document).on('click', '#checkoutTransaksi', function(e) {
            e.preventDefault();
            var kd_transaksi = $(this).attr('data-tb');
            var total_bayar = $(this).attr('data-total');
            $('#kd_transaksi').val(kd_transaksi);
            $('#rp_tb').val(formatNumber(total_bayar));
            $('#total_bayar_hidden').val(total_bayar);
        })

        $('#btnSendtoCart').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id-cart');
            var qty = $('#qty').val();
            $.ajax({
                'url': '<?php echo base_url() ?>Admin/send_to_cart',
                'type': 'POST',
                'data': {
                    id: id,
                    qty: qty
                },
                'dataType': 'json',
                beforeSend: function() {
                    $('#btnSendtoCart').html(`
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                        `);
                    $('#btnSendtoCart').attr(
                        'disabled',
                        'disabled'
                    );
                },
                success: function(respon) {
                    if (respon.status == 'success') {

                        $("#modal_detail_barang").modal('hide');
                        loadCart();

                        dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        });
                    } else {
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        })
                    }
                    $('#btnSendtoCart')
                        .html(
                            `Tambah ke Keranjang`
                        );
                    $('#btnSendtoCart')
                        .removeAttr(
                            'disabled'
                        );
                },
                error: function() {
                    $('#btnSendtoCart')
                        .html(
                            `Tambah ke Keranjang`
                        );
                    $('#btnSendtoCart')
                        .removeAttr(
                            'disabled'
                        );
                    Swal.fire({
                        'title': 'Maaf',
                        'icon': 'error',
                        'text': 'Terjadi kesalahan, coba lagi!',
                    })
                }
            })

        })

        $('#batal_detail').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                'url': '<?php echo base_url() ?>Admin/batal_detail',
                'type': 'POST',
                'data': {
                    id: id
                },
                'dataType': 'json',
                success: function(respon) {
                    loadCart();

                    dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();

                    if (respon.status == 'warning') {
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        'title': 'Maaf',
                        'icon': 'error',
                        'text': 'Terjadi kesalahan, coba lagi!',
                    })
                }
            })

        })

        $('#tambah_detail').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                'url': '<?php echo base_url() ?>Admin/tambah_detail',
                'type': 'POST',
                'data': {
                    id: id
                },
                'dataType': 'json',
                success: function(respon) {
                    loadCart();

                    dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();

                    if (respon.status == 'warning') {
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        'title': 'Maaf',
                        'icon': 'error',
                        'text': 'Terjadi kesalahan, coba lagi!',
                    })
                }
            })

        })

        $('#kurang_detail').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');


        })

        $('#batalTransaksi').on('click', function(e) {
            e.preventDefault();
            var kd = $(this).attr('data-tb');
            Swal.fire({
                title: 'Batalkan transaksi ini ?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        'url': '<?php echo base_url() ?>Admin/batal_transaksi',
                        'type': 'POST',
                        'data': {
                            kd: kd
                        },
                        'dataType': 'json',
                        success: function(respon) {
                            dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();
                            loadCart();

                            Swal.fire({
                                'title': respon
                                    .status,
                                'icon': respon
                                    .status,
                                'text': respon
                                    .message,
                            });
                        },
                        error: function() {
                            Swal.fire({
                                'title': 'Maaf',
                                'icon': 'error',
                                'text': 'Terjadi kesalahan, coba lagi!',
                            })
                        }
                    })
                }
            })

        })

        $('#btnSendCheckout').on('click', function(e) {
            e.preventDefault();
            var data = $('#formCheckout').serialize();
            $.ajax({
                'url': '<?php echo base_url() ?>Admin/send_checkout',
                'type': 'POST',
                'data': data,
                'dataType': 'json',
                beforeSend: function() {
                    $('#btnSendCheckout').html(`
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                        `);
                    $('#btnSendCheckout').attr(
                        'disabled',
                        'disabled'
                    );
                },
                success: function(respon) {
                    if (respon.status == 'success') {

                        $("#modal_checkout_transaksi").modal('hide');
                        loadCart();

                        dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        });
                    } else {
                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        })
                    }
                    $('#btnSendCheckout')
                        .html(
                            `Checkout`
                        );
                    $('#btnSendCheckout')
                        .removeAttr(
                            'disabled'
                        );
                },
                error: function() {
                    $('#btnSendCheckout')
                        .html(
                            `Checkout`
                        );
                    $('#btnSendCheckout')
                        .removeAttr(
                            'disabled'
                        );
                    Swal.fire({
                        'title': 'Maaf',
                        'icon': 'error',
                        'text': 'Terjadi kesalahan, coba lagi!',
                    })
                }
            })

        })


    });

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }

    function loadCart() {
        $('#kd_trans').html('-');
        $('#listCart').html('');
        $('#total_bayar').html('0');
        $('#batalTransaksi').attr('data-tb', "");
        $('#checkoutTransaksi').attr('data-tb', "");
        $('#checkoutTransaksi').attr('data-total', "");

        $('#batalTransaksi').attr('disabled', true);
        $('#checkoutTransaksi').attr('disabled', true);
        $.ajax({
            'url': '<?= base_url('Admin/load_cart') ?>',
            'type': 'POST',
            'dataType': 'json',
            success: function(respon) {
                if (respon.code == 200) {
                    $('#kd_trans').html(respon.cart.kd_transaksi);

                    var dCart = '';
                    var total_bayar = 0;
                    $.each(respon.detail_cart, function(key, val) {
                        dCart += `
                        <div class="row" id="dT-` + val.id + `">
                            <div class="col-5 mb-2">ID Barang : ` + val.id_barang + `<br>
                            ` + val.nama_barang + ` (` + val.qty + `)<br>
                            ` + formatNumber(val.harga) + `
                            </div>
                            <div class="col-4"><span class="text-left">` + formatNumber(val.sub_total) + `</span><br></div>
                            <div class="col-3">
                                <a href="#" onClick="kurang_detail(` + val.id + `)" >(-)</a>
                                <a href="#" onClick="tambah_detail(` + val.id + `)">(+)</a>
                                <br>
                                <a href="#" onClick="batal_detail(` + val.id + `)">batal</a>
                            </div>
                        </div>`;
                        total_bayar = total_bayar + parseInt(val.sub_total);
                    });

                    $('#batalTransaksi').attr('data-tb', respon.cart.kd_transaksi);
                    $('#checkoutTransaksi').attr('data-tb', respon.cart.kd_transaksi);
                    $('#checkoutTransaksi').attr('data-total', total_bayar);
                    $('#listCart').html(dCart);
                    $('#total_bayar').html(formatNumber(total_bayar));
                    $('#total_bayar').attr('data-total', total_bayar);
                    $('#batalTransaksi').attr('disabled', false);
                    $('#checkoutTransaksi').attr('disabled', false);
                }

            },
            error: function() {
                Swal.fire({
                    'title': 'Maaf',
                    'icon': 'error',
                    'text': 'Gagal mengambil data keranjang!',
                })
            }
        })
    }

    function kurang_detail(idx) {
        $.ajax({
            'url': '<?php echo base_url() ?>Admin/kurang_detail',
            'type': 'POST',
            'data': {
                id: idx
            },
            'dataType': 'json',
            success: function(respon) {
                loadCart();

                dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();

                if (respon.status == 'warning') {
                    Swal.fire({
                        'title': respon
                            .status,
                        'icon': respon
                            .status,
                        'text': respon
                            .message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    'title': 'Maaf',
                    'icon': 'error',
                    'text': 'Terjadi kesalahan, coba lagi!',
                })
            }
        })
    }

    function tambah_detail(idx) {
        $.ajax({
            'url': '<?php echo base_url() ?>Admin/tambah_detail',
            'type': 'POST',
            'data': {
                id: idx
            },
            'dataType': 'json',
            success: function(respon) {
                loadCart();

                dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();

                if (respon.status == 'warning') {
                    Swal.fire({
                        'title': respon
                            .status,
                        'icon': respon
                            .status,
                        'text': respon
                            .message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    'title': 'Maaf',
                    'icon': 'error',
                    'text': 'Terjadi kesalahan, coba lagi!',
                })
            }
        })
    }

    function batal_detail(idx) {
        $.ajax({
            'url': '<?php echo base_url() ?>Admin/batal_detail',
            'type': 'POST',
            'data': {
                id: idx
            },
            'dataType': 'json',
            success: function(respon) {
                loadCart();

                dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarangTransaksi?x=" + x).load();

                if (respon.status == 'warning') {
                    Swal.fire({
                        'title': respon
                            .status,
                        'icon': respon
                            .status,
                        'text': respon
                            .message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    'title': 'Maaf',
                    'icon': 'error',
                    'text': 'Terjadi kesalahan, coba lagi!',
                })
            }
        })
    }
</script>