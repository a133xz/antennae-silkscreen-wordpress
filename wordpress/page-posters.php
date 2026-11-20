<?php
/**
 * Template Name: Posters Page
 */
$posters = silkscreen_posters_page_items();
$ss_theme = 'inverse';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell ss-shell--fill" data-theme="inverse">
    <div class="ss-shell__viewport">
    <?php
    $ss_static_mobile_header = true;
    $ss_mobile_menu_light = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--posters">
        <h1 class="sr-only">Posters</h1>
        <div class="ss-posters-stage">
            <ul class="ss-posters-strip" data-posters-strip tabindex="0" aria-label="Posters">
                <?php foreach ($posters as $item) : ?>
                    <li class="ss-poster-card">
                        <a href="<?php echo esc_url($item['permalink']); ?>">
                            <img src="<?php echo esc_url($item['poster']['src']); ?>" alt="<?php echo esc_attr($item['poster']['alt']); ?>" draggable="false" />
                            <h2 class="ss-poster-caption-title"><?php echo esc_html($item['title']); ?></h2>
                            <span class="ss-poster-caption-sub">View project &gt;</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
