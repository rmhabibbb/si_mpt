<style>
    #modal_detail_barang {
        z-index: 10050 !important;
    }

    .swal-container {
        z-index: 12000;
    }

    .swal2-container {
        z-index: 12000;
    }
</style>
<div class="modal fade" id="modal_detail_barang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="div" id="foto-barang"> </div>
                    </div>
                    <div class="col-md-7">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">ID Barang</label>
                                    <input type="text" class="form-control" name="id_barang" id="id_barang" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Nama Barang</label>
                                    <input type="text" class="form-control" name="nama_barang" id="nama_barang" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Kategori</label>
                                    <input type="text" class="form-control" name="nama_kategori" id="nama_kategori" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Jenis</label>
                                    <input type="text" class="form-control" name="jenis" id="jenis" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Ukuran</label>
                                    <input type="text" class="form-control" name="ukuran" id="ukuran" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Stok</label>
                                    <input type="text" class="form-control" name="stok" id="stok" readonly>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="message-text" class="col-form-label">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" id="deskripsi" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="message-text" class="col-form-label">Qty : </label>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" type="number" min="1" max="" value="1" id="qty">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div style=" font-size:120%">
                    <b>Harga : Rp. <span id="harga"></span> </b>
                </div>
                <button type=" button" class="btn btn-primary" id="btnSendtoCart" data-id-cart="">Tambah ke Keranjang</button>
            </div>
        </div>
    </div>
</div>