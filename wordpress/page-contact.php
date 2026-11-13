<?php
/**
 * Template Name: Contact Page
 */
$ss_theme = 'light';
$contact_intro = silkscreen_contact_intro();
$contact_emails = silkscreen_contact_emails();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <?php
    $ss_static_mobile_header = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--contact">
        <div class="ss-page-content ss-page-content--contact">
            <h1 class="ss-contact-lede font-display"><?php echo esc_html($contact_intro); ?></h1>
            <ul class="ss-email-list">
                <?php foreach ($contact_emails as $row) : ?>
                    <li>
                        <span class="ss-email-list__label font-serif"><?php echo esc_html($row['label']); ?></span>
                        <a class="ss-email-list__address font-mono" href="mailto:<?php echo esc_attr($row['address']); ?>"><?php echo esc_html($row['address']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
