<?php
if (!empty($args) && is_array($args)) {
    $project = $args['project'] ?? array();
    $title_tag = $args['title_tag'] ?? 'h3';
    $home_featured = !empty($args['home_featured']);
} else {
    $project = isset($project) ? $project : array();
    $title_tag = isset($title_tag) ? $title_tag : 'h3';
    $home_featured = !empty($home_featured);
}
$url = !empty($project['permalink']) ? $project['permalink'] : silkscreen_project_url($project['slug']);
$meta_class = 'ss-project-meta' . ($home_featured ? ' ss-project-meta--home' : '');
?>
<article class="group">
    <a href="<?php echo esc_url($url); ?>" class="block">
        <div class="ss-still">
            <?php if (!empty($project['image']['src'])) : ?>
                <img src="<?php echo esc_url($project['image']['src']); ?>" alt="<?php echo esc_attr($project['image']['alt']); ?>" loading="lazy" />
            <?php else : ?>
                <span class="font-display" style="display:flex;align-items:center;justify-content:center;height:100%;opacity:.4">Coming soon</span>
            <?php endif; ?>
        </div>
        <div class="<?php echo esc_attr($meta_class); ?>">
            <<?php echo tag_escape($title_tag); ?> class="ss-title font-display"><?php echo esc_html($project['title']); ?></<?php echo tag_escape($title_tag); ?>>
            <?php if (!empty($project['director'])) : ?>
                <p class="ss-directed">
                    <em class="ss-directed__lead">Directed by </em><?php echo esc_html($project['director']); ?>
                </p>
            <?php endif; ?>
        </div>
    </a>
</article>
