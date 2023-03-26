<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">

            <div>
                <h4>Form Tambah User</h4>
                <hr style="border: 1px solid black;" class="mb-5">
            </div>
            <form action="<?= base_url() . 'pemilik/user_store'; ?>" method="post">
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="username" class="col-form-label">Username</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="username" name="username" required>
                        <?= form_error('username', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="password" class="col-form-label">Password</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <?= form_error('password', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="nama_user" class="col-form-label">Nama User</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_user" name="nama_user" required>
                        <?= form_error('nama_user', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="no_hp" class="col-form-label">Nomor HP</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                        <?= form_error('no_hp', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="role" class="col-form-label">Role</label>
                    </div>
                    <div class="col-sm-9">
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Kasir</option>
                            <option value="3">Pemilik</option>
                        </select>
                        <?= form_error('role', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="aktif" class="col-form-label">Aktif</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="checkbox" class="form-check-input" id="aktif" name="aktif" value="1" checked>
                        <?= form_error('aktif', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url(); ?>pemilik/users" class="btn btn-danger">Kembali</a>
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