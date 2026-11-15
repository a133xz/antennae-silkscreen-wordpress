<?php
$post = get_queried_object();
if (!$post) {
    wp_safe_redirect(home_url('/projects/'));
    exit;
}
$project = silkscreen_project_from_post($post);
$ss_theme = 'light';
$sparse = silkscreen_project_is_sparse($project);
$synopsis = silkscreen_project_detail_synopsis($project);
$stills = silkscreen_project_detail_stills($project);
$credits = silkscreen_project_detail_credits($project);
$poster = $project['poster'] ?: $project['image'];
$trailer = $project['trailer_url'];
$tickets = $project['tickets_url'];
$project_actions = array();
if ($trailer !== '') {
    $project_actions[] = array('href' => $trailer, 'label' => 'Watch trailer');
}
if ($tickets !== '') {
    $project_actions[] = array('href' => $tickets, 'label' => 'Book tickets');
}
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="<?php echo esc_attr($ss_theme); ?>">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <article>
            <div class="ss-page-content">
                <header class="ss-project-detail-header">
                    <h1 class="ss-project-title font-display uppercase"><?php echo esc_html($project['title']); ?></h1>
                    <?php if ($synopsis !== '') : ?>
                        <p class="ss-project-synopsis font-serif type-heading-fluid"><?php echo esc_html($synopsis); ?></p>
                    <?php endif; ?>
                </header>
                <?php if ($project_actions) : ?>
                    <div class="ss-project-actions">
                        <span class="ss-project-actions__spacer" aria-hidden="true"></span>
                        <div class="ss-project-actions__row">
                            <?php foreach ($project_actions as $action) :
                                $external = preg_match('#^https?://#', $action['href']);
                                ?>
                                <a class="ss-project-action" href="<?php echo esc_url($action['href']); ?>"<?php echo $external ? ' target="_blank" rel="noreferrer"' : ''; ?>><?php echo esc_html($action['label']); ?><span aria-hidden="true">&gt;</span></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($stills) : ?>
                <div class="ss-project-band" data-project-band-root>
                    <div class="ss-page-content">
                        <div class="ss-project-frame" data-project-slideshow>
                            <?php foreach ($stills as $index => $still) : ?>
                                <img
                                    src="<?php echo esc_url($still['src']); ?>"
                                    alt="<?php echo $index === 0 ? esc_attr($still['alt']) : ''; ?>"
                                    data-still="<?php echo (int) $index; ?>"
                                    data-alt="<?php echo esc_attr($still['alt']); ?>"
                                    <?php echo $index === 0 ? 'class="is-active"' : ''; ?>
                                />
                            <?php endforeach; ?>
                        </div>
                        <div class="ss-project-dots" role="tablist" aria-label="Stills">
                            <?php foreach ($stills as $index => $still) : ?>
                                <button
                                    type="button"
                                    role="tab"
                                    data-still-tab="<?php echo (int) $index; ?>"
                                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-label="<?php echo esc_attr('Still ' . ($index + 1)); ?>"
                                ><?php echo (int) ($index + 1); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($poster || $credits) : ?>
                            <div class="ss-project-detail-grid">
                                <?php if ($poster) : ?>
                                    <div class="ss-project-poster">
                                        <img src="<?php echo esc_url($poster['src']); ?>" alt="<?php echo esc_attr($poster['alt']); ?>" />
                                    </div>
                                <?php else : ?>
                                    <span class="ss-project-actions__spacer" aria-hidden="true"></span>
                                <?php endif; ?>
                                <?php if ($credits) : ?>
                                    <dl class="ss-project-credits">
                                        <?php foreach ($credits as $credit) : ?>
                                            <div class="ss-project-credit">
                                                <dt><?php echo esc_html(silkscreen_credit_label($credit)); ?></dt>
                                                <dd>
                                                    <?php foreach ($credit['names'] as $name) : ?>
                                                        <span><?php echo esc_html($name); ?></span>
                                                    <?php endforeach; ?>
                                                </dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
