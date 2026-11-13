<?php
$settings = silkscreen_settings();
$bug_src = esc_url(silkscreen_asset('brand/bug.svg'));
?>
<footer class="ss-footer">
    <div class="ss-footer__inner">
        <div class="ss-footer__meta font-display">
            <a href="<?php echo esc_url($settings['social_url']); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html($settings['social_handle']); ?>
            </a>
            <span class="ss-footer__sep" aria-hidden="true">/</span>
            <p><?php echo esc_html($settings['copyright']); ?></p>
        </div>
        <span
            class="ss-mark ss-footer__bug"
            aria-hidden="true"
            style="mask-image: url(<?php echo $bug_src; ?>); -webkit-mask-image: url(<?php echo $bug_src; ?>);"
        ></span>
    </div>
</footer>
