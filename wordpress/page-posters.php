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
<div class="ss-shell" data-theme="inverse" style="background:#000;min-height:100svh">
    <?php
    $ss_static_mobile_header = true;
  $ss_mobile_menu_light = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main" style="display:flex;flex:1;align-items:center">
        <h1 class="sr-only">Posters</h1>
        <div class="ss-posters-strip">
            <?php foreach ($posters as $project) : ?>
                <a class="ss-poster-card" href="<?php echo esc_url($project['permalink']); ?>" style="color:inherit;text-decoration:none">
                    <?php if (!empty($project['poster']['src'])) : ?>
                        <img src="<?php echo esc_url($project['poster']['src']); ?>" alt="<?php echo esc_attr($project['poster']['alt']); ?>" loading="lazy" />
                    <?php elseif (!empty($project['image']['src'])) : ?>
                        <img src="<?php echo esc_url($project['image']['src']); ?>" alt="<?php echo esc_attr($project['image']['alt']); ?>" loading="lazy" />
                    <?php endif; ?>
                    <p class="font-display text-micro uppercase" style="margin-top:.75rem"><?php echo esc_html($project['title']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
