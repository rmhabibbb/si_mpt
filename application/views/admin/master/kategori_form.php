<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">

            <div>
                <h4>Form Tambah Kategori</h4>
                <hr style="border: 1px solid black;" class="mb-5">
            </div>
            <form action="<?= base_url() . 'admin/kategori_store'; ?>" method="post">
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="id_kategori" class="col-form-label">ID Kategori</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="id_kategori" name="id_kategori" required>
                        <?= form_error('id_kategori', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="nama_kategori" class="col-form-label">Nama Kategori</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        <?= form_error('nama_kategori', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="deskripsi" class="col-form-label">Deskripsi (Optional)</label>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
                        <?= form_error('deskripsi', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url(); ?>admin/kategori" class="btn btn-danger">Kembali</a>
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