<?php
$ss_theme = isset($ss_theme) ? $ss_theme : 'light';
$ss_surface = isset($ss_surface) ? $ss_surface : '';
$ss_body_class = isset($ss_body_class) ? $ss_body_class : '';
$ss_html_style = $ss_surface !== '' ? ' style="--canvas:' . esc_attr($ss_surface) . '"' : '';
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr($ss_theme); ?>"<?php echo $ss_html_style; ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('silkscreen-theme ' . $ss_body_class); ?><?php echo $ss_surface !== '' ? ' style="--ss-surface:' . esc_attr($ss_surface) . ';--canvas:' . esc_attr($ss_surface) . '"' : ''; ?>>
<?php wp_body_open(); ?>
