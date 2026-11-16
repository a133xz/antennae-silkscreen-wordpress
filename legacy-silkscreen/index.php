<?php
get_header();

$content_id = silkscreen_legacy_content_page_id();
$slides = silkscreen_legacy_get_slides($content_id);
?>

<!-- Logo Top Left -->
<?php
$logo = $content_id ? silkscreen_legacy_acf_scalar($content_id, 'logo') : null;
if (is_array($logo) && !empty($logo['url'])) :
    ?>
    <div class="logo-top-left">
        <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt'] ?? ''); ?>">
    </div>
<?php endif; ?>

<!-- Fullscreen Slideshow Section -->
<section class="slideshow">
    <?php foreach ($slides as $index => $slide) : ?>
        <img
            src="<?php echo esc_url($slide['image']); ?>"
            class="slide-image"
            style="<?php echo $index === 0 ? '' : 'display: none;'; ?>"
            data-title="<?php echo esc_attr($slide['title']); ?>"
            data-client="<?php echo esc_attr($slide['client']); ?>"
            data-date="<?php echo esc_attr($slide['date']); ?>"
            alt="">
    <?php endforeach; ?>

    <?php
    $worm = $content_id ? silkscreen_legacy_acf_scalar($content_id, 'worm') : null;
    $worm_url = is_array($worm) ? ($worm['url'] ?? '') : (is_string($worm) ? $worm : '');
    if ($worm_url !== '') :
        ?>
        <div class="invert-mask"
            style="
                mask-image: url('<?php echo esc_url($worm_url); ?>');
                -webkit-mask-image: url('<?php echo esc_url($worm_url); ?>');
            ">
        </div>
    <?php endif; ?>

    <?php if (!empty($slides)) : ?>
        <div class="text-bottom-left" id="slide-overlay">
            <p>
                <span class="title" id="slide-title"><?php echo esc_html($slides[0]['title']); ?></span><span class="comma">,</span>
                <span class="bold" id="slide-client"><?php echo esc_html($slides[0]['client']); ?></span><span class="comma">,</span>
                <span class="date" id="slide-date"><?php echo esc_html($slides[0]['date']); ?></span>
            </p>
        </div>
    <?php endif; ?>
</section>

<!-- Scrollable Bottom Section -->
<section class="bottom">
    <div class="left">
        <?php
        if ($content_id) {
            $footer_body = silkscreen_legacy_acf_scalar($content_id, 'footer_body');
            if (is_string($footer_body) && $footer_body !== '') {
                echo wp_kses_post($footer_body);
            } else {
                echo '<p>Silkscreen is a film production company that prioritises artist-driven storytelling, cultural engagement, and bold creative expression.</p>';
            }
        }
        ?>
    </div>
    <div class="right">
        <?php
        if ($content_id) {
            $footer_email = silkscreen_legacy_acf_scalar($content_id, 'footer_email');
            $footer_instagram = silkscreen_legacy_acf_scalar($content_id, 'footer_instagram');
            $footer_twitter = silkscreen_legacy_acf_scalar($content_id, 'footer_twitter');

            if (is_string($footer_email) && $footer_email !== '') {
                echo '<a href="mailto:' . antispambot($footer_email) . '">' . antispambot($footer_email) . '</a>';
            } else {
                echo '<a href="mailto:hello@silkscreen.com">hello@silkscreen.com</a>';
            }

            if ($footer_instagram || $footer_twitter) {
                echo '<div class="social-icons">';
                if (is_string($footer_instagram) && $footer_instagram !== '') {
                    echo '<a href="' . esc_url($footer_instagram) . '" target="_blank" rel="noopener noreferrer">';
                    echo '<img src="' . esc_url(get_template_directory_uri() . '/images/instagram.svg') . '" alt="Instagram" />';
                    echo '</a>';
                }
                if (is_string($footer_twitter) && $footer_twitter !== '') {
                    echo '<a href="' . esc_url($footer_twitter) . '" target="_blank" rel="noopener noreferrer">';
                    echo '<img src="' . esc_url(get_template_directory_uri() . '/images/twitter.svg') . '" alt="Twitter" />';
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        ?>
    </div>
</section>

<?php get_footer(); ?>