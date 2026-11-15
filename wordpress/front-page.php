<?php
$ss_theme = 'light';
$projects = silkscreen_featured_projects(2);
$news = silkscreen_featured_news(3);
$products = silkscreen_shop_available() ? silkscreen_featured_products(3) : array();
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell ss-shell--home" data-theme="light">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/hero.php'; ?>
    <?php
    $ss_header_after_hero = true;
    require get_template_directory() . '/template-parts/site-header.php';
    ?>
    <main class="ss-main ss-main--home">
        <div class="ss-page-content">
            <section class="ss-home-section ss-home-section--first">
                <?php
                $label = 'Featured projects';
                $href = home_url('/projects/');
                $action = 'View all projects';
                $action_below_md = true;
                $layout_split_two = true;
                require get_template_directory() . '/template-parts/section-header.php';
                ?>
                <ul class="ss-grid-930 ss-grid-930--home">
                    <?php foreach ($projects as $project) : ?>
                        <li><?php get_template_part('template-parts/project-card', null, array('project' => $project, 'title_tag' => 'h3', 'home_featured' => true)); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="ss-section-action-below">
                    <?php get_template_part('template-parts/text-link', null, array('href' => home_url('/projects/'), 'label' => 'View all projects')); ?>
                </div>
            </section>

            <section class="ss-home-section ss-home-section--news">
                <?php
                $label = 'Latest news';
                $href = home_url('/news/');
                $action = 'View all news';
                $action_below_md = true;
                $layout_split_two = true;
                require get_template_directory() . '/template-parts/section-header.php';
                get_template_part('template-parts/news-masonry', null, array('items' => $news, 'title_tag' => 'h3'));
                ?>
                <div class="ss-section-action-below">
                    <?php get_template_part('template-parts/text-link', null, array('href' => home_url('/news/'), 'label' => 'View all news')); ?>
                </div>
            </section>
        </div>

        <?php if ($products) : ?>
            <?php get_template_part('template-parts/home-shop-section', null, array('products' => $products)); ?>
        <?php else : ?>
            <div class="ss-home-shop-spacer" aria-hidden="true"></div>
        <?php endif; ?>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
