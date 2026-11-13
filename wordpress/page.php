<?php
$ss_theme = 'light';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell" data-theme="light">
    <div class="ss-shell__viewport">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content" style="padding:4rem 0">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <h1 class="font-display type-heading-fluid uppercase"><?php the_title(); ?></h1>
            <?php endwhile; endif; ?>
        </div>
    </main>
    </div>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
