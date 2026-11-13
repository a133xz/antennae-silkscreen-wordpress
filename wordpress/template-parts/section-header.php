<?php
$label = isset($label) ? $label : '';
$href = isset($href) ? $href : '';
$action = isset($action) ? $action : '';
$action_below = !empty($action_below_md);
?>
<div class="ss-section-header">
    <h2><?php echo esc_html($label); ?></h2>
    <?php if ($href && $action) : ?>
        <a class="ss-text-link" href="<?php echo esc_url($href); ?>"><?php echo esc_html($action); ?></a>
    <?php endif; ?>
</div>
<?php if ($action_below && $href && $action) : ?>
    <div class="ss-section-action-below">
        <a class="ss-text-link" href="<?php echo esc_url($href); ?>"><?php echo esc_html($action); ?></a>
    </div>
<?php endif; ?>
