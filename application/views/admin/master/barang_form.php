<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">

            <div>
                <h4>Form Tambah Barang</h4>
                <hr style="border: 1px solid black;" class="mb-5">
            </div>
            <form id="formAddBarang" enctype="multipart/form-data">
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="nama_barang" class="col-form-label">Nama Barang</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        <?= form_error('nama_barang', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="id_kategori" class="col-form-label">Kategori</label>
                    </div>
                    <div class="col-sm-9">
                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($list_kategori as $kat) : ?>
                                <option value="<?= $kat->id_kategori ?>"><?= $kat->nama_kategori ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('id_kategori', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="harga" class="col-form-label">Harga</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control rupiah" id="rp_harga" required>
                        <input type="hidden" id="harga" name="harga">
                        <?= form_error('harga', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="stok" class="col-form-label">Stok</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" class="form-control  id=" stok" name="stok" min="0" required>
                        <?= form_error('stok', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="deskripsi" class="col-form-label">Deskripsi</label>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
                        <?= form_error('deskripsi', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="foto" class="col-form-label">Foto</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" id="foto" name="foto">
                        <?= form_error('foto', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="button" id="btnSubmit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url(); ?>admin/users" class="btn btn-danger">Kembali</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


<style>
    .m-approval {
        z-index: 10050 !important;
    }

    .swal-container {
        z-index: 12000;
    }

    .swal2-container {
        z-index: 12000;
    }

    ;
</style>


<!-- Sweet Alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if ($this->session->flashdata('success')) : ?>
    <script>
        Swal.fire({
            title: "Success",
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
    $(document).ready(function() {
        $('#btnSubmit').on('click', function(e) {
            e.preventDefault();
            var data = new FormData();
            data = $('#formAddBarang').serialize();
            var file = $('#foto').prop('files')[0];
            data.push({
                name: 'foto',
                value: file
            });

            console.log(data)
            $.ajax({
                'url': '<?php echo base_url() ?>admin/barang_store',
                'type': 'POST',
                'data': data,
                'dataType': 'json',
                success: function(respon) {
                    if (respon.status ==
                        'success') {

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
                                .icon,
                            'html': respon
                                .message,
                        })
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
    })
</script>