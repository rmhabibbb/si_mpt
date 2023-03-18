</div>

<footer class="footer pt-3 text-center" style="margin-top:400px; background-color:#F8F9FA;">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                    ©<script>
                        document.write(new Date().getFullYear());
                    </script>,
                    Sistem Informasi Manajemen Penjualan Terdistribusi
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
<!-- Sweet Alert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- sidebar show -->
<script>
    const element = document.getElementById('link-<?= $index ?>');
    element.scrollIntoView({
        block: "center"
    });
</script>
</body>

</html>