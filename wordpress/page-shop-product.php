<?php
$slug = sanitize_title((string) get_query_var('silkscreen_shop_product'));
$product = $slug ? silkscreen_shop_product($slug) : null;
if (!$product || !silkscreen_shop_available()) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    require get_template_directory() . '/404.php';
    return;
}
$ss_theme = 'light';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content" style="padding:2.5rem 0 4rem;max-width:960px">
            <h1 class="font-display type-heading-fluid uppercase"><?php echo esc_html($product['title']); ?></h1>
            <p class="ss-product-card__price" style="margin:1rem 0 2rem"><?php echo esc_html(silkscreen_format_price($product['price'])); ?></p>
            <img src="<?php echo esc_url($product['image']['src']); ?>" alt="<?php echo esc_attr($product['image']['alt']); ?>" style="width:100%;max-width:480px;height:auto;display:block" />
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
