<?php
$ss_theme = 'light';
$settings = silkscreen_settings();
$projects = silkscreen_featured_projects(2);
$news = silkscreen_featured_news(3);
$products = ($settings['shop_enabled'] && silkscreen_shop_available()) ? silkscreen_featured_products(3) : array();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <?php require get_template_directory() . '/template-parts/hero.php'; ?>
    <?php
    $ss_header_on_hero = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main">
        <div class="ss-page-content" style="padding-top:3rem">
            <section style="padding-top:4rem">
                <?php
                $label = 'Featured projects';
                $href = home_url('/projects/');
                $action = 'View all projects';
                $action_below_md = true;
                require get_template_directory() . '/template-parts/section-header.php';
                ?>
                <ul class="ss-grid-930" style="list-style:none;margin:0;padding:0">
                    <?php foreach ($projects as $project) : ?>
                        <li><?php get_template_part('template-parts/project-card', null, array('project' => $project, 'title_tag' => 'h3')); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="ss-section-action-below">
                    <a class="ss-text-link" href="<?php echo esc_url(home_url('/projects/')); ?>">View all projects</a>
                </div>
            </section>

            <section style="margin-top:5rem">
                <?php
                $label = 'Latest news';
                $href = home_url('/news/');
                $action = 'View all news';
                $action_below_md = true;
                require get_template_directory() . '/template-parts/section-header.php';
                get_template_part('template-parts/news-masonry', null, array('items' => $news, 'title_tag' => 'h3'));
                ?>
                <div class="ss-section-action-below">
                    <a class="ss-text-link" href="<?php echo esc_url(home_url('/news/')); ?>">View all news</a>
                </div>
            </section>
        </div>

        <?php if ($products) : ?>
            <section class="ss-page-content" style="margin-top:5rem;margin-bottom:6rem">
                <?php
                $label = 'Shop';
                $href = silkscreen_shop_url();
                $action = 'View shop';
                $action_below_md = false;
                require get_template_directory() . '/template-parts/section-header.php';
                ?>
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
            </section>
        <?php else : ?>
            <div style="margin-bottom:6rem" aria-hidden="true"></div>
        <?php endif; ?>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
