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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-4">
                            <a href="<?= base_url(); ?>admin/barang_form" class="btn btn-primary mb-5"><i class="fas fa-user-plus mr-5"></i>Tambah</a>
                        </div>
                        <div class="col-md-3">
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
                                    <th>ID Barang</th>
                                    <th>Foto</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
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

        var x = $('#id_kategori').find(":selected").val();
        var dataAplikasi = $('#data-table-barang').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],

            "ajax": {
                "url": "<?= base_url(); ?>Admin/getDataBarang",
                "type": "POST",
                "data": {
                    x: x
                },

            },


            "columnDefs": [{
                "targets": [0],
                "orderable": false,
            }, {
                "targets": [5],
                "orderable": false,
            }, {
                "targets": [1],
                "orderable": false,
            }, {
                "targets": [6],
                "orderable": false,
            }, ],

        });

        $('#id_kategori').on('change', function() {
            x = $('#id_kategori').find(":selected").val();
            dataAplikasi.ajax.url("<?= base_url(); ?>Admin/getDataBarang?x=" + x).load();
        })


    });

    function hapusBarang(ids) {

        $('.hapus-barang').on('click', function() {
            Swal.fire({
                title: 'Apakah kamu yakin ?',
                text: "kamu akan menghapus data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = ids;
                    $.ajax({
                        'url': '<?= base_url('admin/barang_delete') ?>',
                        'type': 'POST',
                        'data': {
                            id: id
                        },
                        success: function() {
                            window
                                .location
                                .replace(
                                    "<?= base_url('admin/barang') ?>"
                                );
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Data berhasil dihapus.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1000

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

                }
            })
        })
    }
</script>