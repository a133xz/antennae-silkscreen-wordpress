<?php
$primary = array(
    array('href' => home_url('/projects/'), 'label' => 'Projects'),
);
if (silkscreen_shop_available()) {
    $primary[] = array('href' => silkscreen_shop_url(), 'label' => 'Shop');
}
$primary[] = array('href' => home_url('/news/'), 'label' => 'News');
$primary[] = array('href' => home_url('/posters/'), 'label' => 'Posters');
$secondary = array(
    array('href' => home_url('/about/'), 'label' => 'About'),
    array('href' => silkscreen_contact_url(), 'label' => 'Contact'),
);
$nav_items = array_merge($primary, $secondary);
$header_class = isset($ss_header_class) ? $ss_header_class : '';
$static_mobile = !empty($ss_static_mobile_header);
$after_hero = !empty($ss_header_after_hero);
$fix_mobile = !$static_mobile && !$after_hero;
$transparent = !empty($ss_header_transparent_mobile);
$menu_light = !empty($ss_mobile_menu_light);
$logo_blend = !empty($ss_logo_blend);
?>
<header
    data-site-header
    <?php echo $static_mobile ? ' data-static-mobile-header' : ''; ?>
    <?php echo $fix_mobile ? ' data-fix-mobile-header' : ''; ?>
    class="<?php echo esc_attr(trim($header_class . ($after_hero ? ' ss-header--after-hero' : ''))); ?>"
>
    <?php if ($fix_mobile && !$transparent) : ?>
        <div class="ss-header-fill-wrap" aria-hidden="true">
            <div data-header-fill class="ss-header-fill"></div>
        </div>
    <?php endif; ?>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="ss-logo relative z-10 <?php echo $logo_blend ? 'ss-logo--blend' : ''; ?>" aria-label="Silkscreen">
        <?php get_template_part('template-parts/logo', null, array('blend' => $logo_blend)); ?>
    </a>
    <nav data-site-nav class="relative z-10" aria-label="Primary">
        <ul class="ss-site-nav-group">
            <?php foreach ($primary as $item) : ?>
                <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <ul class="ss-site-nav-group">
            <?php foreach ($secondary as $item) : ?>
                <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="ss-mobile-nav-bar relative z-10">
        <button type="button" class="ss-mobile-nav-toggle" data-mobile-nav-open aria-expanded="false" aria-controls="ss-mobile-nav" aria-haspopup="dialog">Menu</button>
    </div>
</header>
<div
    id="ss-mobile-nav"
    data-mobile-nav-panel
    <?php echo $menu_light ? ' data-menu-light' : ''; ?>
    role="dialog"
    aria-modal="true"
    aria-label="Menu"
    aria-hidden="true"
    data-visible="false"
>
    <div class="ss-mobile-nav-panel__top<?php echo ($static_mobile || $fix_mobile) ? ' ss-mobile-nav-panel__top--notch' : ''; ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="ss-logo" aria-label="Silkscreen">
            <?php get_template_part('template-parts/logo', null, array('blend' => $logo_blend)); ?>
        </a>
        <button type="button" class="ss-mobile-nav-toggle" data-mobile-nav-close>Close</button>
    </div>
    <nav class="ss-mobile-nav-panel__links" aria-label="Mobile">
        <ul data-site-nav>
            <?php foreach ($nav_items as $item) : ?>
                <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo esc_html($item['label']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</div>
