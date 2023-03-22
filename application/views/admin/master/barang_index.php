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
                    <a href="<?= base_url(); ?>admin/barang_form" class="btn btn-primary mb-5"><i class="fas fa-user-plus mr-5"></i>Tambah</a>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm" id="example" style="width:100%">
                            <thead style="background-color:#051E34;color:#fff;">
                                <tr>
                                    <th>ID Barang</th>
                                    <th>Foto</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list_barang as $key => $value) : ?>
                                    <tr id="<?= $value->id_barang; ?>">
                                        <td style="text-align:center; vertical-align:middle"><?= $value->id_barang; ?></td>
                                        <td style="text-align:center">
                                            <?php if ($value->foto == NULL) { ?>
                                                <img alt="Image placeholder" src="<?= base_url('assets/default.png') ?>">
                                            <?php } else { ?>
                                                <img alt="Image placeholder" src="<?= base_url('assets/barang/') ?><?= $value->foto ?>">
                                            <?php } ?>
                                        </td>
                                        <td><?= $value->nama_barang; ?></td>
                                        <td><?= $this->Kategori_m->get_row(['id_kategori' => $value->id_kategori])->nama_kategori; ?></td>
                                        <td><?= number_format($value->harga, 0, '.', ','); ?></td>
                                        <td><?= $value->stok; ?></td>
                                        <td><?= $value->deskripsi; ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/barang_edit/') . $value->id_barang; ?>" class="btn btn-success me-2"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:" class="btn btn-danger hapus-barang" data-id="<?= $value->id_barang; ?>"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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

<script>
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
                var id = $(this).attr('data-id');
                $.ajax({
                    'url': '<?= base_url('admin/barang_delete') ?>',
                    'type': 'POST',
                    'data': {
                        id: id
                    },
                    success: function() {
                        $('#' + id).fadeOut();
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
</script>

<!-- Datatables -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            responsive: true
        });
    });
</script>