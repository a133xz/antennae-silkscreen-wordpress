<?php

add_action('admin_menu', function () {
    if (!current_user_can('manage_options')) {
        return;
    }
    add_menu_page(
        'Silkscreen demo',
        'Demo',
        'manage_options',
        'silkscreen-demo',
        'silkscreen_admin_demo_page',
        'dashicons-database-import',
        81
    );
});

add_action('admin_post_silkscreen_import_demo', function () {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to import demo content.'));
    }
    check_admin_referer('silkscreen_import_demo');

    $force = !empty($_POST['silkscreen_demo_force']);
    $messages = array();
    $ok = false;
    $skipped = false;

    try {
        $result = silkscreen_import_demo_data(array(
            'force' => $force,
            'log' => static function ($line) use (&$messages) {
                $messages[] = (string) $line;
            },
        ));
        $ok = !empty($result['ok']);
        $skipped = !empty($result['skipped']);
    } catch (Throwable $e) {
        $messages[] = 'Error: ' . $e->getMessage();
    }

    set_transient(
        'silkscreen_demo_import_log_' . get_current_user_id(),
        array(
            'ok' => $ok,
            'skipped' => $skipped,
            'messages' => $messages,
        ),
        120
    );

    wp_safe_redirect(admin_url('admin.php?page=silkscreen-demo&imported=1'));
    exit;
});

function silkscreen_admin_demo_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $seeded = get_option('silkscreen_demo_seeded') === '1';
    $acf_ok = function_exists('get_field');
    $records_ok = is_file(get_template_directory() . '/seed/records.php');

    $log_block = null;
    if (isset($_GET['imported']) && $_GET['imported'] === '1') {
        $log_block = get_transient('silkscreen_demo_import_log_' . get_current_user_id());
        delete_transient('silkscreen_demo_import_log_' . get_current_user_id());
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Demo', 'silkscreen-2026'); ?></h1>
        <p><?php esc_html_e('Load sample pages, projects, news, and shop content from the theme (same data as the Next.js reference).', 'silkscreen-2026'); ?></p>

        <?php if (!$acf_ok) : ?>
            <div class="notice notice-error"><p><?php esc_html_e('Install and activate Advanced Custom Fields before importing.', 'silkscreen-2026'); ?></p></div>
        <?php elseif (!$records_ok) : ?>
            <div class="notice notice-error"><p><?php esc_html_e('Missing seed/records.php in the theme. Re-deploy the theme.', 'silkscreen-2026'); ?></p></div>
        <?php endif; ?>

        <?php if ($seeded) : ?>
            <div class="notice notice-info"><p><?php esc_html_e('Demo content was imported previously. Check “Replace existing demo” to remove old demo posts and import again.', 'silkscreen-2026'); ?></p></div>
        <?php endif; ?>

        <?php if (is_array($log_block)) : ?>
            <?php
            $class = !empty($log_block['ok']) && empty($log_block['skipped']) ? 'notice-success' : 'notice-warning';
            if (!empty($log_block['ok']) && !empty($log_block['skipped'])) {
                $class = 'notice-info';
            }
            if (empty($log_block['ok'])) {
                $class = 'notice-error';
            }
            ?>
            <div class="notice <?php echo esc_attr($class); ?> is-dismissible">
                <p><strong><?php esc_html_e('Import log', 'silkscreen-2026'); ?></strong></p>
                <pre style="white-space:pre-wrap;margin:0;font-size:12px;max-height:320px;overflow:auto;"><?php
                    echo esc_html(implode("\n", $log_block['messages'] ?? array()));
                ?></pre>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="silkscreen_import_demo" />
            <?php wp_nonce_field('silkscreen_import_demo'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Replace existing demo', 'silkscreen-2026'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="silkscreen_demo_force" value="1" />
                            <?php esc_html_e('Delete posts marked as demo, then import fresh (same as --force in CLI).', 'silkscreen-2026'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <?php
            submit_button(
                __('Import demo data', 'silkscreen-2026'),
                'primary',
                'submit',
                true,
                $acf_ok && $records_ok ? array() : array('disabled' => 'disabled')
            );
            ?>
        </form>
    </div>
    <?php
}