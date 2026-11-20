<?php
if (!empty($args) && is_array($args) && !empty($args['projects'])) {
    $projects = $args['projects'];
} else {
    $projects = isset($projects) ? $projects : silkscreen_listed_projects();
}
$count = count($projects);
?>
<ul class="ss-projects-mobile">
    <?php foreach ($projects as $project) : ?>
        <li>
            <?php get_template_part('template-parts/project-card', null, array('project' => $project, 'title_tag' => 'h3', 'home_featured' => true)); ?>
        </li>
    <?php endforeach; ?>
</ul>
<div class="ss-projects-pile" data-projects-pile data-count="<?php echo (int) $count; ?>">
    <?php foreach ($projects as $index => $project) : ?>
        <div class="ss-pile-title-cell<?php echo $index === $count - 1 ? ' ss-pile-title-cell--last' : ''; ?>" data-pile-index="<?php echo (int) $index; ?>">
            <div class="ss-pile-title" data-pile-sticky>
                <a href="<?php echo esc_url($project['permalink']); ?>" class="ss-pile-title__link">
                    <h2 class="ss-pile-title__name font-display type-heading-fluid"><?php echo esc_html($project['title']); ?></h2>
                    <?php if (!empty($project['director'])) : ?>
                        <p class="ss-directed"><em class="ss-directed__lead">Directed by</em><span class="ss-directed__name"><?php echo esc_html($project['director']); ?></span></p>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach ($projects as $index => $project) : ?>
        <a href="<?php echo esc_url($project['permalink']); ?>" class="ss-still<?php echo $index < $count - 1 ? ' ss-still--spaced' : ''; ?>" data-pile-still="<?php echo (int) $index; ?>">
            <?php if (!empty($project['image']['src'])) : ?>
                <img src="<?php echo esc_url($project['image']['src']); ?>" alt="<?php echo esc_attr($project['image']['alt']); ?>" loading="lazy" />
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
