<?php
/**
 * Template Name: About Page
 */
$ss_theme = 'accent';
$about_statement = silkscreen_about_statement();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div id="ss-about-layer" data-about-layer aria-hidden="true"></div>
<div class="ss-shell ss-about-content" data-theme="accent">
    <?php
    $ss_static_mobile_header = true;
    $ss_header_transparent_mobile = true;
    $ss_mobile_menu_light = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--about">
        <div class="ss-page-content ss-page-content--about">
            <h1 class="font-display type-lede-fluid ss-about-statement">
                <?php echo esc_html($about_statement); ?>
            </h1>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
