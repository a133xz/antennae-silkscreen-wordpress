<?php
$ss_theme = isset($ss_theme) ? $ss_theme : 'light';
$ss_surface = isset($ss_surface) ? $ss_surface : '';
$ss_body_class = isset($ss_body_class) ? $ss_body_class : '';
$ss_theme_color = isset($ss_theme_color) ? $ss_theme_color : '';
$ss_html_style = '';
if ($ss_theme_color !== '') {
    $ss_html_style = ' style="background-color:' . esc_attr($ss_theme_color) . ';--canvas:' . esc_attr($ss_theme_color) . '"';
} elseif ($ss_surface !== '') {
    $ss_html_style = ' style="--canvas:' . esc_attr($ss_surface) . '"';
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr($ss_theme); ?>"<?php echo $ss_html_style; ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <?php if ($ss_theme_color !== '') : ?>
    <meta name="theme-color" content="<?php echo esc_attr($ss_theme_color); ?>">
    <?php endif; ?>
</head>
<body <?php body_class('silkscreen-theme ' . $ss_body_class); ?><?php
if ($ss_theme_color !== '') {
    echo ' style="background-color:' . esc_attr($ss_theme_color) . ';--canvas:' . esc_attr($ss_theme_color) . '"';
} elseif ($ss_surface !== '') {
    echo ' style="--ss-surface:' . esc_attr($ss_surface) . ';--canvas:' . esc_attr($ss_surface) . '"';
}
?>>
<?php wp_body_open(); ?>
