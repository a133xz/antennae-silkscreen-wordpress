<?php
if (!silkscreen_shop_available()) {
    require get_template_directory() . '/template-parts/shop-coming-soon.php';
    return;
}
$products = silkscreen_shop_products();
$ss_theme = 'inverse';
$shop_bg = silkscreen_asset('media/shop-bg.png');
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell ss-shell--transparent" data-theme="inverse">
    <div class="ss-shop-backdrop" style="background-image:url(<?php echo esc_url($shop_bg); ?>)" aria-hidden="true"></div>
    <div class="ss-shell__viewport">
    <?php
    $ss_header_transparent_mobile = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content ss-page-content--shop">
            <h1 class="sr-only">Shop</h1>
            <ul class="ss-shop-grid">
                <?php foreach ($products as $product) : ?>
                    <li>
                        <a class="ss-shop-product" href="<?php echo esc_url(silkscreen_shop_url($product['slug'])); ?>">
                            <div class="ss-shop-product__frame">
                                <?php if (!empty($product['image']['src'])) : ?>
                                    <img src="<?php echo esc_url($product['image']['src']); ?>" alt="<?php echo esc_attr($product['image']['alt']); ?>" draggable="false" />
                                <?php endif; ?>
                            </div>
                            <h2 class="ss-poster-caption-title"><?php echo esc_html($product['title']); ?></h2>
                            <p class="ss-poster-caption-sub"><?php echo esc_html(silkscreen_format_price($product['price'])); ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
