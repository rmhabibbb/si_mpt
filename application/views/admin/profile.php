<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">
            <form id="formProfile">

                <input type="hidden" class="form-control" name="username_old" value="<?= $profil->username ?>">
                <div class="row mb-4">
                    <div class="col-sm-3">
                        <label for="username" class="col-form-label">Username</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="username" name="username" value="<?= $profil->username ?>" required>
                        <?= form_error('username', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="nama_user" class="col-form-label">Nama User</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?= $profil->nama_user ?>" required>
                        <?= form_error('nama_user', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="no_hp" class="col-form-label">Nomor HP</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?= $profil->no_hp ?>" required>
                        <?= form_error('no_hp', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary" id="btnSaveProfile">Simpan</button>
                    </div>
                    <div class="col-sm-3 align-items-end">
                        <a data-bs-target="#modal-ganti-password" data-bs-toggle="modal" id="ganti-password" class="btn btn-primary ">Ganti Password</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade m-approval" id="modal-ganti-password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Form Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" style="white-space: nowrap;">
                        <tr>
                            <th>Password Lama</th>
                            <td> <input type="password" class="form-control" id="password_old" name="password_old"></td>
                        </tr>
                        <tr>
                            <th>Password Baru</th>
                            <td> <input type="password" class="form-control" id="password" name="password"></td>
                        </tr>
                        <tr>
                            <th>Ulangi Password Baru</th>
                            <td> <input type="password" class="form-control" id="password_confirm" name="password_confirm"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-center">
                <a href="javascript:void(0);" class="btn btn-success btn-sm" id="btnGantiPassword" data-id="<?= $profil->username ?>">Submit</a>
            </div>
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
    $(document).ready(function() {
        $('#btnGantiPassword').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var password_old = $('#password_old').val();
            var password = $('#password').val();
            var password_confirm = $('#password_confirm').val();
            $.ajax({
                'url': '<?php echo base_url() ?>admin/profile_update_pass',
                'type': 'POST',
                'data': {
                    id: id,
                    password_old: password_old,
                    password: password,
                    password_confirm: password_confirm,
                },
                'dataType': 'json',
                success: function(respon) {

                    if (respon.status == 'success') {
                        $("#modal-ganti-password").modal('hide');

                        $('#password_old').val('');
                        $('#password').val('');
                        $('#password_confirm').val('');

                        Swal.fire({
                            'title': respon
                                .status,
                            'icon': respon
                                .status,
                            'text': respon
                                .message,
                        });
                    } else if (respon.status == 'warning') {

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

        $('#btnSaveProfile').on('click', function(e) {
            e.preventDefault();
            var data = $('#formProfile').serialize();
            console.log(data)
            $.ajax({
                'url': '<?php echo base_url() ?>admin/profile_update',
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