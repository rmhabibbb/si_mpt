</div>

<footer class="footer py-3 text-center  mt-2" style=" background-color:#F8F9FA;">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-12 mb-lg-0 ">
                <div class="copyright text-center text-sm text-muted text-lg-center">
                    ©<script>
                        document.write(new Date().getFullYear());
                    </script>,
                    Lisan Collection
                </div>
            </div>
        </div>
</footer>

</main>

<!--   Core JS Files   -->
<script src="<?= base_url(); ?>assets/js/core/popper.min.js"></script>
<script src="<?= base_url(); ?>assets/js/core/bootstrap.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= base_url(); ?>assets/js/plugins/smooth-scrollbar.min.js"></script>
<!-- <script src="<?= base_url(); ?>assets/js/plugins/chartjs.min.js"></script> -->
<script src="<?= base_url(); ?>assets/js/DataTables/datatables.min.js"></script>

<!-- Github buttons -->


<!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="<?= base_url(); ?>assets/js/argon-dashboard.min.js?v=2.0.4"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/autoNumeric.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery.mask.js"></script>
<!-- Sweet Alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- sidebar show -->
<script>
    const element = document.getElementById('link-<?= $link ?>');
    element.scrollIntoView({
        block: "center"
    });

    var optRupiah = {
        aSep: '.',
        aDec: ',',
        aForm: true,
        vMax: '9999999999999',
        vMin: '-9999999999999'
    };
    $('.rupiah').autoNumeric('init', optRupiah);
    $("#rp_harga").keyup(function() {
        var number = $('#rp_harga').autoNumeric('get');
        $('#harga').val(number);
    });
</script>
</body>

</html>