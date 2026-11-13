<?php
/**
 * Template Name: About Page
 */
$ss_theme = 'accent';
$settings = silkscreen_settings();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div id="ss-about-layer" data-about-layer aria-hidden="true"></div>
<div class="ss-shell ss-about-content" data-theme="accent" style="background:transparent;min-height:100svh">
    <?php
    $ss_static_mobile_header = true;
    $ss_mobile_menu_light = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main" style="display:flex;flex:1;align-items:center">
        <div class="ss-page-content" style="width:100%;padding:4rem 0;max-width:57.8125rem;margin:0 auto">
            <h1 class="font-display type-lede-fluid" style="color:var(--ink);margin:0;text-transform:uppercase;letter-spacing:-0.02em">
                <?php echo esc_html($settings['statement']); ?>
            </h1>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
