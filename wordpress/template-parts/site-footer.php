<?php $settings = silkscreen_settings(); ?>
<footer class="ss-footer">
    <div class="ss-footer__inner">
        <div class="ss-footer__meta font-display">
            <a href="<?php echo esc_url($settings['social_url']); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html($settings['social_handle']); ?>
            </a>
            <p><?php echo esc_html($settings['copyright']); ?></p>
        </div>
        <img class="ss-footer__bug" src="<?php echo esc_url(silkscreen_asset('brand/bug.svg')); ?>" alt="" width="120" height="56" />
    </div>
</footer>
