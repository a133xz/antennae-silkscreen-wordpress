<?php

add_action('add_meta_boxes', function () {
    add_meta_box(
        'silkscreen_project_posters',
        'Posters',
        'silkscreen_project_posters_meta_box',
        SILKSCREEN_PROJECT_POST_TYPE,
        'normal',
        'core'
    );
}, 25);

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== SILKSCREEN_PROJECT_POST_TYPE) {
        return;
    }
    silkscreen_admin_enqueue_row_reorder($hook);
    wp_enqueue_media();
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $script = $dir . '/js/admin-project-posters.js';
    if (!is_readable($script)) {
        return;
    }
    wp_enqueue_script(
        'silkscreen-project-posters',
        $uri . '/js/admin-project-posters.js',
        array('jquery', 'silkscreen-admin-row-reorder'),
        filemtime($script),
        true
    );
    wp_localize_script('silkscreen-project-posters', 'silkscreenPosters', array(
        'max' => SILKSCREEN_PROJECT_POSTER_SLOTS,
        'frameTitle' => 'Choose a poster',
        'frameButton' => 'Use image',
    ));
});

function silkscreen_project_posters_meta_box(WP_Post $post) {
    wp_nonce_field('silkscreen_save_project_posters', 'silkscreen_posters_nonce');
    $ids = array();
    $saved = metadata_exists('post', $post->ID, SILKSCREEN_PROJECT_POSTERS_META);
    if ($saved || silkscreen_bool('on_posters', $post->ID, false)) {
        $ids = silkscreen_project_poster_attachment_ids($post->ID);
    }
    ?>
    <p class="description" style="margin-top:0;">
        Images for the posters page, in order. Use the arrows to reorder. Up to <?php echo (int) SILKSCREEN_PROJECT_POSTER_SLOTS; ?> posters.
    </p>
    <p>
        <label>
            <input type="checkbox" name="ss_project_on_posters" value="1" <?php checked(silkscreen_bool('on_posters', $post->ID, false)); ?> />
            Show on posters page
        </label>
    </p>
    <div id="ss-project-posters" data-ss-posters-root>
        <ul class="ss-poster-rows" data-ss-posters-rows>
            <?php if ($ids) : ?>
                <?php foreach ($ids as $attachment_id) : ?>
                    <?php silkscreen_project_poster_row_markup($attachment_id); ?>
                <?php endforeach; ?>
            <?php else : ?>
                <?php silkscreen_project_poster_row_markup(0); ?>
            <?php endif; ?>
        </ul>
        <p>
            <button type="button" class="button button-secondary" data-ss-posters-add>Add poster</button>
        </p>
    </div>
    <template id="ss-poster-row-tpl">
        <?php silkscreen_project_poster_row_markup(0); ?>
    </template>
    <?php silkscreen_admin_reorder_styles(); ?>
    <style>
        .ss-poster-rows { list-style: none; margin: 0; padding: 0; max-width: 760px; }
        .ss-poster-row {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            background: #fff;
            padding: 10px 12px;
            margin: 0 0 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .ss-poster-row__thumb {
            width: 48px;
            height: 72px;
            object-fit: cover;
            background: #f0f0f1;
            flex-shrink: 0;
        }
        .ss-poster-row__thumb--empty { display: block; }
        .ss-poster-row__meta { flex: 1; min-width: 0; font-size: 12px; color: #50575e; }
    </style>
    <?php
}

function silkscreen_project_poster_row_markup($attachment_id) {
    $attachment_id = (int) $attachment_id;
    $thumb = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'thumbnail') : '';
    $title = $attachment_id ? get_the_title($attachment_id) : '';
    ?>
    <li class="ss-poster-row" data-ss-poster-row>
        <?php if ($thumb) : ?>
            <img class="ss-poster-row__thumb" src="<?php echo esc_url($thumb); ?>" alt="" data-ss-poster-preview />
        <?php else : ?>
            <span class="ss-poster-row__thumb ss-poster-row__thumb--empty" data-ss-poster-preview aria-hidden="true"></span>
        <?php endif; ?>
        <div class="ss-poster-row__meta" data-ss-poster-label><?php echo esc_html($title ?: 'No image selected'); ?></div>
        <div class="ss-admin-row-actions">
            <?php silkscreen_admin_row_order_buttons(); ?>
            <button type="button" class="button button-secondary button-small" data-ss-poster-pick>Choose</button>
            <button type="button" class="button button-small" data-ss-poster-remove>Remove</button>
        </div>
        <input type="hidden" name="ss_project_poster_ids[]" value="<?php echo (int) $attachment_id; ?>" data-ss-poster-id />
    </li>
    <?php
}

add_action('save_post_' . SILKSCREEN_PROJECT_POST_TYPE, function ($post_id) {
    if (!isset($_POST['silkscreen_posters_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['silkscreen_posters_nonce'])), 'silkscreen_save_project_posters')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (!isset($_POST['post_ID']) || (int) $_POST['post_ID'] !== (int) $post_id) {
        return;
    }

    $ids = array();
    if (!empty($_POST['ss_project_poster_ids']) && is_array($_POST['ss_project_poster_ids'])) {
        foreach ($_POST['ss_project_poster_ids'] as $raw_id) {
            $id = (int) $raw_id;
            if ($id > 0) {
                $ids[] = $id;
            }
            if (count($ids) >= SILKSCREEN_PROJECT_POSTER_SLOTS) {
                break;
            }
        }
    }

    update_post_meta($post_id, SILKSCREEN_PROJECT_POSTERS_META, wp_json_encode($ids));

    $on_posters = !empty($_POST['ss_project_on_posters']) ? 1 : 0;
    if (function_exists('update_field')) {
        update_field('on_posters', $on_posters, $post_id);
    } else {
        update_post_meta($post_id, 'on_posters', $on_posters);
    }
}, 20, 1);