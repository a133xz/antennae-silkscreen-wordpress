<?php
$settings = silkscreen_settings();
$primary = array(
    array('href' => home_url('/projects/'), 'label' => 'Projects'),
);
if (!empty($settings['shop_enabled']) && silkscreen_shop_available()) {
    $primary[] = array('href' => silkscreen_shop_url(), 'label' => 'Shop');
}
$primary[] = array('href' => home_url('/news/'), 'label' => 'News');
if (!empty($settings['posters_enabled'])) {
    $primary[] = array('href' => home_url('/posters/'), 'label' => 'Posters');
}
$secondary = array(
    array('href' => home_url('/about/'), 'label' => 'About'),
    array('href' => silkscreen_contact_url(), 'label' => 'Contact'),
);
$nav_items = array_merge($primary, $secondary);
$header_class = isset($ss_header_class) ? $ss_header_class : '';
$static_mobile = !empty($ss_static_mobile_header);
$on_hero = !empty($ss_header_on_hero);
$menu_light = !empty($ss_mobile_menu_light);
?>
<header
    data-site-header
    <?php echo $static_mobile ? ' data-static-mobile-header' : ''; ?>
    class="<?php echo esc_attr(trim($header_class . ($on_hero ? ' ss-header--on-hero' : ''))); ?>"
>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="ss-logo relative z-10 <?php echo !empty($ss_logo_blend) ? 'ss-logo--blend' : ''; ?>">
        <img src="<?php echo esc_url(silkscreen_asset('brand/wordmark.svg')); ?>" alt="Silkscreen" width="160" height="32" />
    </a>
    <nav data-site-nav class="relative z-10" aria-label="Primary">
        <div>
            <?php foreach ($primary as $item) : ?>
                <a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a>
            <?php endforeach; ?>
        </div>
        <div>
            <?php foreach ($secondary as $item) : ?>
                <a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a>
            <?php endforeach; ?>
        </div>
    </nav>
    <button type="button" class="ss-mobile-nav-toggle relative z-10" data-mobile-nav-open aria-expanded="false" aria-controls="ss-mobile-nav">Menu</button>
</header>
<div id="ss-mobile-nav" data-mobile-nav-panel<?php echo $menu_light ? ' data-menu-light' : ''; ?> aria-hidden="true">
    <button type="button" class="ss-mobile-nav-toggle" data-mobile-nav-close style="align-self:flex-end">Close</button>
    <nav aria-label="Mobile">
        <?php foreach ($nav_items as $item) : ?>
            <a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</div>
