<?php
$ss_theme = 'light';
$ss_surface = '#f9f9f9';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell ss-shell--surface footer-tail" data-theme="light" style="background:#f9f9f9">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content" style="padding-top:2.5rem">
            <h1 class="sr-only">News</h1>
            <?php get_template_part('template-parts/news-masonry', null, array('items' => silkscreen_news_query(), 'scroll_reveal' => true)); ?>
        </div>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
