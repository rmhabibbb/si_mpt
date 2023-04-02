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
                                <label for="message-text" class="col-form-label">Nama Supplier</label>
                                <input type="text" class="form-control" value="<?= $trans->nama_supplier ?>" readonly name="nama_supplier" id="nama_supplier">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Kontak Supplier</label>
                                <input type="text" class="form-control" value="<?= $trans->kontak_supplier ?>" readonly name="kontak_supplier" id="kontak_supplier">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Alamat Supplier</label>
                                <textarea class="form-control" name="alamat_supplier" id="alamat_supplier" readonly> <?= $trans->alamat_supplier ?></textarea>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($dtrans as $row) { ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <?php if ($row->foto == NULL || $row->foto == '') { ?>
                                        <span style="text-align:center;">
                                            <img alt="Image placeholder" src="<?= base_url('assets/default.png') ?>">
                                        </span>
                                    <?php  } else { ?>
                                        <span style="text-align:center">
                                            <img alt="Image placeholder" src="<?= base_url('assets/barang') ?>/<?= $row->foto ?>">
                                        </span> ''
                                    <?php } ?>
                                </td>
                                <td><?= $row->nama_barang ?></td>
                                <td><?= number_format($row->harga, 2, ',', '.') ?></td>
                                <td><?= $row->qty ?></td>
                                <td><?= $row->qty_return ?></td>
                                <td><?= number_format($row->sub_total, 2, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
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
        var dataAplikasi = $('#data-table-transaksi').DataTable({

            "columnDefs": [{
                "targets": [1],
                "orderable": false,
            }, ],

        });
    });

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
</script>