<?php
$ss_theme = 'light';
$ss_surface = '#f9f9f9';
require get_template_directory() . '/template-parts/document-open.php';
?>
<div class="ss-shell ss-shell--surface" data-theme="light" style="--ss-surface:#f9f9f9;background:#f9f9f9">
    <?php require get_template_directory() . '/template-parts/site-header.php'; ?>
    <main class="ss-main ss-main--pad-header">
        <div class="ss-page-content" style="padding-top:2.5rem;padding-bottom:200px">
            <h1 class="sr-only">Projects</h1>
            <?php get_template_part('template-parts/projects-scroll', null, array('projects' => silkscreen_listed_projects())); ?>
        </div>
    </main>
    <?php require get_template_directory() . '/template-parts/site-footer.php'; ?>
</div>
<?php require get_template_directory() . '/template-parts/document-close.php';
