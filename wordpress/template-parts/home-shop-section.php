<?php
$products = (!empty($args['products']) && is_array($args['products'])) ? $args['products'] : array();
if (!$products) {
    return;
}
$order = array('lighter', 'postcards', 'skateboard');
usort($products, function ($a, $b) use ($order) {
    $ai = array_search(strtolower($a['title']), $order, true);
    $bi = array_search(strtolower($b['title']), $order, true);
    $ai = $ai === false ? 99 : $ai;
    $bi = $bi === false ? 99 : $bi;
    return $ai <=> $bi;
});
$shop_url = silkscreen_shop_url();
$bg = silkscreen_asset('media/home-shop.png');
?>
<div class="ss-home-shop-outer">
    <div class="ss-page-content">
        <?php
        $label = 'Shop';
        $href = $shop_url;
        $action = 'View all products';
        $action_below_md = true;
        $layout_split_two = true;
        require get_template_directory() . '/template-parts/section-header.php';
        ?>
        <div class="ss-home-shop-stage">
            <div class="ss-home-shop-stage__bg" style="background-image:url(<?php echo esc_url($bg); ?>)" aria-hidden="true"></div>
            <ul class="ss-home-shop-products">
                <?php foreach ($products as $product) : ?>
                    <li>
                        <a href="<?php echo esc_url(silkscreen_shop_url($product['slug'])); ?>" class="ss-home-shop-product">
                            <div class="ss-home-shop-product__frame">
                                <img src="<?php echo esc_url($product['image']['src']); ?>" alt="<?php echo esc_attr($product['image']['alt']); ?>" />
                            </div>
                            <h3 class="ss-poster-caption-title"><?php echo esc_html($product['title']); ?></h3>
                            <p class="ss-poster-caption-sub"><?php echo esc_html(silkscreen_format_price($product['price'])); ?></p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="ss-section-action-below">
            <a class="ss-text-link" href="<?php echo esc_url($shop_url); ?>">View all products</a>
        </div>
    </div>
</div>
