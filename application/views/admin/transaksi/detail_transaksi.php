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
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Kode Transaksi</label>
                                <input type="text" value="<?= $trans->kd_transaksi ?>" id="kd_transaksi" name="kd_transaksi" readonly class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Nama Customer</label>
                                <input type="text" class="form-control" value="<?= $trans->nama_customer ?>" readonly name="nama_customer" id="nama_customer">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Kontak Customer</label>
                                <input type="text" class="form-control" value="<?= $trans->kontak_customer ?>" readonly name="kontak_customer" id="kontak_customer">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Alamat Customer</label>
                                <textarea class="form-control" name="alamat_customer" id="alamat_customer" readonly> <?= $trans->alamat_customer ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="message-text" class="col-form-label">Total Bayar : </label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Rp.</span>
                                <input class="form-control bg-white rupiah" type="text" id="rp_tb" readonly value="<?= $trans->total_bayar ?>" readonly>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm" id="data-table-transaksi" style="width:100%">
                    <thead style="background-color:#051E34;color:#fff;">
                        <tr>
                            <th>No.</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Jumlah Beli</th>
                            <th>Jumlah Return</th>
                            <th>Sub Total</th>
                            <th style="width: 50px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/transaksi/modal/retur_modal') ?>
<!-- Sweet Alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/autoNumeric.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.mask.js"></script>
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
<script>
    $(document).ready(function() {
        var optRupiah = {
            aSep: '.',
            aDec: ',',
            aForm: true,
            vMax: '9999999999999',
            vMin: '-9999999999999'
        };
        $('.rupiah').autoNumeric('init', optRupiah);
        var kd_trans = $('#kd_transaksi').val();
        var dataAplikasi = $('#data-table-transaksi').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= base_url(); ?>Admin/getDetailTransaksi",
                "type": "POST",
                "data": {
                    kd: kd_trans
                },

            },


            "columnDefs": [{
                "targets": [0],
                "orderable": false,
            }, {
                "targets": [1],
                "orderable": false,
            }, {
                "targets": [7],
                "orderable": false,
            }, ],

        });

        $(document).on('click', '#view-modal_retur_barang', function(e) {
            e.preventDefault();
            var barang = $(this).attr('data-barang');
            var qty = parseInt($(this).attr('data-qty')) - parseInt($(this).attr('data-qty-return'));
            var harga = $(this).attr('data-harga');
            var qtyreturn = $(this).attr('data-qty-return');
            var id = $(this).attr('data-id');
            var sub = $(this).attr('data-sub');
            $('#btnReturn').attr('data-id', '');
            $('#sub_total').html(formatNumber(0))

            $.ajax({
                'url': '<?php echo base_url(); ?>Admin/get_barang_by_id',
                'type': 'POST',
                'data': {
                    id: barang
                },
                'dataType': 'json',
                success: function(respon) {

                    $('#id_barang').val(respon.barang.id_barang)
                    $('#nama_barang').val(respon.barang.nama_barang)
                    $('#nama_kategori').val(respon.barang.nama_kategori)
                    $('#jenis').val(respon.barang.jenis)
                    $('#ukuran').val(respon.barang.ukuran)
                    $('#deskripsi').val(respon.barang.deskripsi)
                    $('#stok').val(respon.barang.stok)
                    $('#sub_total').html(formatNumber(sub))
                    $('#qty').val(qty);
                    $('#qty_return').val(0);
                    $('#btnReturn').attr('data-id', id);
                    $('#qty_return').autoNumeric('init', {
                        aForm: true,
                        vMax: qty,
                        vMin: 0
                    });


                    if (respon.barang.foto != null) {
                        $('#foto-barang').html(`<a href="<?= base_url('assets/barang/'); ?>` + respon.barang.foto + `" data-fancybox="brg-foto" data-caption="Foto Barang">
                            <img src="<?= base_url('assets/barang/'); ?>` + respon.barang.foto + `" alt="Foto Barang" class="w-100" style="width:100%">
                        </a>`);
                    } else {
                        $('#foto-barang').html(`<a href="<?= base_url('assets/default.png'); ?>" data-fancybox="brg-foto" data-caption="Foto Barang">
                            <img src="<?= base_url('assets/default.png'); ?>" alt="Foto Barang" class="w-100" style="width:100%">
                        </a>`);
                    }

                    $('#qty_return').keyup(function() {
                        var sub_total = sub - ($(this).val() * harga);
                        $('#sub_total').html(formatNumber(sub_total))
                    })


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

        $('#btnReturn').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var qty_return = $('#qty_return').val();
            var qty = $('#qty').val();
            console.log(qty)
            if (qty_return == 0) {
                Swal.fire({
                    'title': 'Warning',
                    'icon': 'warning',
                    'text': 'Jumlah Retur tidak boleh kosong!',
                });
            } else {
                $.ajax({
                    'url': '<?php echo base_url() ?>Admin/send_return',
                    'type': 'POST',
                    'data': {
                        id: id,
                        qty_return: qty_return,
                        kd_transaksi: kd_trans

                    },
                    'dataType': 'json',
                    beforeSend: function() {
                        $('#btnReturn').html(`
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                        `);
                        $('#btnReturn').attr(
                            'disabled',
                            'disabled'
                        );
                    },
                    success: function(respon) {
                        if (respon.status == 'success') {

                            $("#rp_tb").autoNumeric('set', respon.total_bayar);
                            $("#modal_retur_barang").modal('hide');

                            dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDetailTransaksi").load();
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
                        $('#btnReturn')
                            .html(
                                `Retur Barang`
                            );
                        $('#btnReturn')
                            .removeAttr(
                                'disabled'
                            );
                    },
                    error: function() {
                        $('#btnReturn')
                            .html(
                                `Retur Barang`
                            );
                        $('#btnReturn')
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
            }
        })
    });

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
</script>