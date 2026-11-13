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
            <?php get_template_part('template-parts/project-card', null, array('project' => $project, 'title_tag' => 'h3')); ?>
        </li>
    <?php endforeach; ?>
</ul>
<div class="ss-projects-pile" data-projects-pile data-count="<?php echo (int) $count; ?>">
    <?php foreach ($projects as $index => $project) : ?>
        <div class="ss-pile-title-cell" data-pile-index="<?php echo (int) $index; ?>">
            <div class="ss-pile-title" data-pile-sticky>
                <a href="<?php echo esc_url($project['permalink']); ?>" class="font-display type-heading-fluid uppercase" style="text-decoration:none;color:inherit;display:block">
                    <span class="block"><?php echo esc_html($project['title']); ?></span>
                    <?php if (!empty($project['director'])) : ?>
                        <span class="ss-directed block" style="margin-top:7px"><em>Directed by </em><?php echo esc_html($project['director']); ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php foreach ($projects as $index => $project) : ?>
        <a href="<?php echo esc_url($project['permalink']); ?>" class="ss-still" data-pile-still="<?php echo (int) $index; ?>" style="text-decoration:none;color:inherit<?php echo $index < $count - 1 ? ';padding-bottom:18px' : ''; ?>">
            <?php if (!empty($project['image']['src'])) : ?>
                <img src="<?php echo esc_url($project['image']['src']); ?>" alt="<?php echo esc_attr($project['image']['alt']); ?>" loading="lazy" />
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
