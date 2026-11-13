<?php
/**
 * Template Name: Posters Page
 */
$settings = silkscreen_settings();
if (empty($settings['posters_enabled'])) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    require get_template_directory() . '/404.php';
    return;
}
$posters = silkscreen_poster_projects();
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
                <?php foreach ($posters as $project) : ?>
                    <li class="ss-poster-card">
                        <a href="<?php echo esc_url($project['permalink']); ?>">
                            <?php
                            $img = !empty($project['poster']['src']) ? $project['poster'] : ($project['image'] ?? null);
                            if ($img && !empty($img['src'])) :
                                ?>
                                <img src="<?php echo esc_url($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" draggable="false" />
                            <?php endif; ?>
                            <h2 class="ss-poster-caption-title"><?php echo esc_html($project['title']); ?></h2>
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
