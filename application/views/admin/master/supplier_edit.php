<div class="container-fluid">
    <div class="row">
        <div class="col-12 bg-white p-4 rounded shadow">

            <div>
                <h4>Form Edit Supplier</h4>
                <hr style="border: 1px solid black;" class="mb-5">
            </div>
            <form id="formSupplier">
                <input type="hidden" name="id_supplierx" value="<?= $supplier->id_supplier ?>">
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="id_supplier" class="col-form-label">ID Supplier</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="id_supplier" name="id_supplier" value="<?= $supplier->id_supplier ?>" required>
                        <?= form_error('id_supplier', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="nama_supplier" class="col-form-label">Nama Supplier</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="<?= $supplier->nama_supplier ?>" required>
                        <?= form_error('nama_supplier', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="kontak" class="col-form-label">Kontak (No HP/WA)</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="kontak" name="kontak" required value="<?= $supplier->kontak ?>">
                        <?= form_error('kontak', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4 mb-4">
                    <div class="col-sm-3">
                        <label for="alamat" class="col-form-label">Alamat</label>
                    </div>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="alamat" name="alamat"><?= $supplier->alamat ?></textarea>
                        <?= form_error('alamat', '<small class="text-danger ">*', '</small>'); ?>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="button" id="btnEditSupplier" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url(); ?>admin/supplier" class="btn btn-danger">Kembali</a>
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
        $('#btnEditSupplier').on('click', function(e) {
            e.preventDefault();
            var data = $('#formSupplier').serialize();
            $.ajax({
                'url': '<?php echo base_url() ?>admin/supplier_update',
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

                        window.location.href = "<?= base_url('admin/supplier_edit/') ?>" + respon.id;
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