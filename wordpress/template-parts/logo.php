<?php
$blend = false;
if (!empty($args) && is_array($args)) {
    $blend = !empty($args['blend']);
} elseif (!empty($logo_blend)) {
    $blend = true;
}
$wordmark = esc_url(silkscreen_asset('brand/wordmark.svg'));
?>
<?php if ($blend) : ?>
    <img
        class="ss-logo__mark ss-logo__mark--blend"
        src="<?php echo $wordmark; ?>"
        alt=""
        width="1245"
        height="170"
        decoding="async"
    />
<?php else : ?>
    <span
        class="ss-mark ss-logo__mark"
        aria-hidden="true"
        style="mask-image: url(<?php echo $wordmark; ?>); -webkit-mask-image: url(<?php echo $wordmark; ?>);"
    ></span>
<?php endif; ?>
