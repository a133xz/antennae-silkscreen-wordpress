<?php
$settings = silkscreen_settings();
$slides = $settings['hero'];
?>
<section class="ss-hero" aria-roledescription="carousel" aria-label="Silkscreen">
    <button type="button" class="ss-hero__scroll-hit" data-hero-scroll aria-label="Scroll to navigation"></button>
    <?php foreach ($slides as $i => $slide) : ?>
        <img
            class="ss-hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?>"
            src="<?php echo esc_url($slide['src']); ?>"
            alt="<?php echo esc_attr($i === 0 ? $slide['alt'] : ''); ?>"
            width="1920"
            height="1080"
        />
    <?php endforeach; ?>
    <img
        class="ss-hero__wordmark"
        src="<?php echo esc_url(silkscreen_asset('brand/wordmark.svg')); ?>"
        alt=""
        width="1245"
        height="170"
        decoding="async"
        fetchpriority="high"
    />
</section>
