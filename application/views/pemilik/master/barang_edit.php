<style>
    #file-ip-1-preview {
        width: 200px;
        height: 200px;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">

            <div>
                <h4>Form Edit Barang</h4>
                <hr style="border: 1px solid black;" class="mb-5">
            </div>
            <form id="formAddBarang" enctype="multipart/form-data" action="<?= base_url() ?>admin/barang_update" method="post">
                <input type="hidden" name="id_barang" value="<?= $barang->id_barang ?>">
                <input type="hidden" name="path" value="<?= $barang->foto ?>">
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="nama_barang" class="col-form-label">Nama Barang</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= $barang->nama_barang ?>" required>
                        <?= form_error('nama_barang', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="id_kategori" class="col-form-label">Kategori</label>
                    </div>
                    <div class="col-sm-9">
                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                            <?php foreach ($list_kategori as $kat) : ?>
                                <option value="<?= $kat->id_kategori ?>" <?= ($barang->id_kategori == $kat->id_kategori) ? 'selected' : '' ?>><?= $kat->nama_kategori ?></option>
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
                        <input type="text" class="form-control rupiah" id="rp_harga" value="<?= $barang->harga ?>" required>
                        <input type="hidden" id="harga" name="harga" value="<?= $barang->harga ?>">
                        <?= form_error('harga', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="stok" class="col-form-label">Stok</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="number" class="form-control  id=" stok" name="stok" min="0" required value="<?= $barang->stok ?>">
                        <?= form_error('stok', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="deskripsi" class="col-form-label">Deskripsi</label>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="deskripsi" name="deskripsi"><?= $barang->deskripsi ?></textarea>
                        <?= form_error('deskripsi', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="foto" class="col-form-label">Foto</label>
                    </div>
                    <div class="col-sm-9">
                        <div class="preview">
                            <?php if ($barang->foto == NULL) { ?>
                                <img alt="Image placeholder" id="file-ip-1-preview" src="<?= base_url('assets/default.png') ?>">
                            <?php } else { ?>
                                <img alt="Image placeholder" id="file-ip-1-preview" src="<?= base_url('assets/barang/') ?><?= $barang->foto ?>">
                            <?php } ?>
                            <input type="file" class="form-control mt-2" name="foto" id="file-ip-1" accept="image/*" onchange="showPreview(event);">
                            <?= form_error('foto', '<small class="text-danger ">*', '</small>'); ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <input type="submit" id="btnSubmit" class="btn btn-primary" value="Simpan">
                        <a href="<?= base_url(); ?>admin/barang" class="btn btn-danger">Kembali</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

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
    function showPreview(event) {
        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("file-ip-1-preview");
            preview.src = src;

        }
    }
</script>