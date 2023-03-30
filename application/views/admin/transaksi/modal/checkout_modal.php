<style>
    #modal_checkout_transaksi {
        z-index: 10050 !important;
    }

    .swal-container {
        z-index: 12000;
    }

    .swal2-container {
        z-index: 12000;
    }
</style>
<div class="modal fade" id="modal_checkout_transaksi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Checkout Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCheckout">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label">Kode Transaksi</label>
                                        <input type="text" class="form-control" name="kd_transaksi" id="kd_transaksi" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label">Nama Customer</label>
                                        <input type="text" class="form-control" name="nama_customer" id="nama_customer">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label">Kontak Customer</label>
                                        <input type="text" class="form-control" name="kontak_customer" id="kontak_customer">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label">Alamat Customer</label>
                                        <textarea class="form-control" name="alamat_customer" id="alamat_customer"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="message-text" class="col-form-label">Total Bayar : </label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp.</span>
                                        <input class="form-control bg-white" type="text" id="rp_tb" readonly>
                                        <input type="hidden" id="total_bayar_hidden" name="total_bayar">
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type=" button" class="btn btn-primary" id="btnSendCheckout" data-id-cart="">Checkout</button>
            </div>
        </div>
    </div>
</div>