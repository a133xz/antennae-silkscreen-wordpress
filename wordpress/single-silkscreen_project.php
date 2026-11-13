<?php
$post = get_queried_object();
if (!$post) {
    wp_safe_redirect(home_url('/projects/'));
    exit;
}
$project = silkscreen_project_from_post($post);
$ss_theme = $project['detail_theme'] === 'accent' ? 'light' : $project['detail_theme'];
require get_template_directory() . '/template-parts/document-open.php';
$stills = $project['stills'] ? $project['stills'] : ($project['image'] ? array($project['image']) : array());
$poster = $project['poster'] ?: $project['image'];
?>
<div class="ss-shell" data-theme="<?php echo esc_attr($ss_theme); ?>">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <article>
            <div class="ss-page-content ss-project-detail-header">
                <h1 class="font-display type-hero-fluid uppercase" style="margin:0;letter-spacing:-0.03em;line-height:1.07"><?php echo esc_html($project['title']); ?></h1>
                <?php if ($project['synopsis']) : ?>
                    <p class="font-serif type-heading-fluid" style="max-width:62rem;margin:26px auto 0;font-style:italic;font-weight:500;line-height:1.375"><?php echo esc_html($project['synopsis']); ?></p>
                <?php endif; ?>
            </div>
            <?php foreach ($stills as $still) : ?>
                <div class="ss-page-content" style="margin-top:2rem">
                    <img src="<?php echo esc_url($still['src']); ?>" alt="<?php echo esc_attr($still['alt']); ?>" style="width:100%;height:auto;display:block" />
                </div>
            <?php endforeach; ?>
            <?php if ($poster || $project['credits']) : ?>
                <div class="ss-page-content" style="margin:3rem auto;max-width:1200px;display:grid;gap:3rem">
                    <?php if ($poster) : ?>
                        <img src="<?php echo esc_url($poster['src']); ?>" alt="<?php echo esc_attr($poster['alt']); ?>" style="max-width:455px;width:100%;height:auto;display:block;margin:0 auto" />
                    <?php endif; ?>
                    <?php if ($project['credits']) : ?>
                        <div class="font-mono text-micro" style="text-transform:uppercase;text-align:center">
                            <?php foreach ($project['credits'] as $credit) : ?>
                                <p style="margin:0 0 1rem">
                                    <strong><?php echo esc_html($credit['custom_label'] ?: str_replace('-', ' ', $credit['role'])); ?></strong><br />
                                    <?php echo esc_html(implode(', ', $credit['names'])); ?>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </article>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
