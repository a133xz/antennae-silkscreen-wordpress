<?php
/**
 * Template Name: Contact Page
 */
$ss_theme = 'light';
$settings = silkscreen_settings();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <?php
    $ss_static_mobile_header = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main" style="display:flex;flex:1;align-items:center">
        <div class="ss-page-content" style="width:100%;padding:4rem 0">
            <h1 class="ss-contact-lede font-display"><?php echo esc_html($settings['contact_intro']); ?></h1>
            <div class="ss-email-list font-mono" style="margin-top:2.5rem;text-transform:uppercase">
                <?php foreach ($settings['emails'] as $row) : ?>
                    <a href="mailto:<?php echo esc_attr($row['address']); ?>"><?php echo esc_html($row['label']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
