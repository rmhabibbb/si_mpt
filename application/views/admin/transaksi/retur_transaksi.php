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
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-sm" id="data-table-transaksi" style="width:100%">
                    <thead style="background-color:#051E34;color:#fff;">
                        <tr>
                            <th>Kode Transaksi</th>
                            <th>Nama Customer</th>
                            <th>Total Bayar</th>
                            <th>Tanggal Transaksi</th>
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
<script>
    $(document).ready(function() {
        var dataAplikasi = $('#data-table-transaksi').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= base_url(); ?>Admin/getDataTransaksi",
                "type": "POST",

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
            }, {
                "targets": [4],
                "orderable": false,
            }, ],

        });
    });

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
</script>