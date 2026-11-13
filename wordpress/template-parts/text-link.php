<?php
if (!empty($args) && is_array($args)) {
    $href = $args['href'] ?? '';
    $label = $args['label'] ?? '';
} else {
    $href = isset($href) ? $href : '';
    $label = isset($label) ? $label : '';
}
if ($href === '' || $label === '') {
    return;
}
?>
<a class="ss-text-link" href="<?php echo esc_url($href); ?>">
    <?php echo esc_html($label); ?><span class="ss-text-link__chevron" aria-hidden="true">&gt;</span>
</a>
