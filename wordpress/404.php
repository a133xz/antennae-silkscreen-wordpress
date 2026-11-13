<?php
$ss_theme = 'light';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header" style="display:flex;align-items:center;justify-content:center;min-height:50vh">
        <p class="font-display type-heading-fluid uppercase">Page not found</p>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
