<?php
if (!silkscreen_shop_available()) {
    require get_template_directory() . '/template-parts/shop-coming-soon.php';
    return;
}
$products = silkscreen_shop_products();
$ss_theme = 'light';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content" style="padding:2.5rem 0 4rem">
            <h1 class="font-display type-heading-fluid uppercase" style="margin:0 0 2rem">Shop</h1>
            <ul class="ss-shop-grid" style="list-style:none;margin:0;padding:0">
                <?php foreach ($products as $product) : ?>
                    <li>
                        <a href="<?php echo esc_url(silkscreen_shop_url($product['slug'])); ?>" style="color:inherit;text-decoration:none">
                            <img src="<?php echo esc_url($product['image']['src']); ?>" alt="<?php echo esc_attr($product['image']['alt']); ?>" style="width:100%;height:auto;display:block" />
                            <p class="font-display type-heading-fluid uppercase" style="margin:.75rem 0 .25rem"><?php echo esc_html($product['title']); ?></p>
                            <p class="ss-product-card__price"><?php echo esc_html(silkscreen_format_price($product['price'])); ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
