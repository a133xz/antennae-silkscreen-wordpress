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
    <div class="ss-shell__viewport">
    <?php
    $ss_static_mobile_header = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--contact">
        <div class="ss-main__center">
        <div class="ss-page-content ss-page-content--contact">
            <div class="ss-contact-inner">
                <h1 class="ss-contact-lede font-display"><?php echo esc_html($contact_intro); ?></h1>
                <div class="ss-contact-emails">
                    <ul class="ss-email-list">
                        <?php foreach ($contact_emails as $row) : ?>
                            <li>
                                <span class="ss-email-list__label font-serif"><?php echo esc_html($row['label']); ?></span>
                                <a class="ss-email-list__address font-mono" href="mailto:<?php echo esc_attr($row['address']); ?>"><?php echo esc_html($row['address']); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
