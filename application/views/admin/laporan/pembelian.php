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
    <div class="row mb-3">
        <div class="col-md-12 bg-white p-4 rounded shadow">
            <?php echo form_open_multipart('admin/pdf_pembelian'); ?>
            <div class="row">
                <div class="col-md-7 mt-2">
                    <div class="row">
                        <div class="col-sm-2">
                            <label for="start_date">Tanggal : </label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <input class="form-control" type="date" name="start_date" id="start_date">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" type="date" name="end_date" id="end_date" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-2">
                    <a href="javascript:" class="btn btn-primary me-2" id="filter">Filter</a>
                    <!-- <button type="submit" class="btn btn-success" id="export"><span class="far fa-file-excel me-2"></span>Export</button> -->
                    <button type="submit" name="pdf" class="btn btn-danger" id="pdf"><span class="far fa-fa-download me-2"></span>Download PDF</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 bg-white p-4 rounded shadow">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <input type="hidden" value="SEMUA" id="status_proposal">
                        <table class="table table-hover table-bordered table-sm" id="data-table-transaksi" style="width:100%">
                            <thead style="background-color:#051E34;color:#fff;">
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Nama Supplier</th>
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

        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        var dataAplikasi = $('#data-table-transaksi').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= base_url(); ?>Admin/filterLaporanSupply",
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

        $('#start_date').change(function() {
            $('#end_date').attr('min', $(this).val());
            $('#end_date').attr('readonly', false);
            start_date = $('#start_date').val();
            if ($('#end_date').val() == "" || ($(this).val() > $('#end_date').val())) {
                $('#end_date').val($(this).val());
                end_date = $('#end_date').val();
            }
        })

        $('#end_date').change(function() {
            end_date = $('#end_date').val();
        })

        $('#filter').click(function() {
            dataAplikasi.ajax.url("<?= base_url(); ?>Admin/filterLaporanSupply?start_date=" + start_date + "&end_date=" + end_date).load();
        })
    });

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
</script>