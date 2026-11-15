<?php
/**
 * Fallback template — never render the home layout unless this is the front page.
 */
if (is_front_page()) {
    require get_template_directory() . '/front-page.php';
    return;
}

if (is_singular()) {
    require get_template_directory() . '/page.php';
    return;
}

require get_template_directory() . '/404.php';