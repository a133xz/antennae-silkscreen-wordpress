<?php
$label = isset($label) ? $label : '';
$href = isset($href) ? $href : '';
$action = isset($action) ? $action : '';
$action_below = !empty($action_below_md);
?>
<div class="ss-section-header<?php echo !empty($layout_split_two) ? ' ss-section-header--split-two' : ''; ?>">
    <h2><?php echo esc_html($label); ?></h2>
    <?php if ($href && $action) : ?>
        <div class="ss-section-header__action<?php echo $action_below ? ' ss-section-header__action--below-md' : ''; ?>">
            <?php get_template_part('template-parts/text-link', null, array('href' => $href, 'label' => $action)); ?>
        </div>
    <?php endif; ?>
</div>
