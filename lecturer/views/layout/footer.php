    </div><!-- /content-wrapper -->
</div><!-- /main-content -->

<script src="<?= BASE_URL ?>/public/asset/css/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/asset/js/main.js"></script>
<script>
document.getElementById('toggleSidebar')?.addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
});
</script>
<?php if (isset($extraJs)): ?>
<script><?= $extraJs ?></script>
<?php endif; ?>
</body>
</html>
