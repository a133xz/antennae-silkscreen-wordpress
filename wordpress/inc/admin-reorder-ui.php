<?php

function silkscreen_admin_enqueue_row_reorder($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $dir = get_template_directory();
    $uri = get_template_directory_uri();
    $script = $dir . '/js/admin-project-reorder.js';
    if (!is_readable($script)) {
        return;
    }
    wp_enqueue_script(
        'silkscreen-admin-row-reorder',
        $uri . '/js/admin-project-reorder.js',
        array('jquery'),
        filemtime($script),
        true
    );
}

function silkscreen_admin_row_order_buttons() {
    ?>
    <div class="ss-row-order" data-ss-row-order role="group" aria-label="Reorder">
        <button type="button" class="ss-row-order__btn" data-ss-row-up aria-label="Move up" title="Move up">
            <span class="ss-row-order__icon" aria-hidden="true"></span>
        </button>
        <button type="button" class="ss-row-order__btn ss-row-order__btn--down" data-ss-row-down aria-label="Move down" title="Move down">
            <span class="ss-row-order__icon" aria-hidden="true"></span>
        </button>
    </div>
    <?php
}

function silkscreen_admin_reorder_styles() {
    ?>
    <style>
        .ss-row-order {
            display: inline-flex;
            flex-direction: row;
            flex-shrink: 0;
            align-self: center;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            background: #fff;
            overflow: hidden;
        }
        .ss-row-order__btn {
            appearance: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            color: #646970;
            cursor: pointer;
            line-height: 1;
            transition: background-color 0.12s ease, color 0.12s ease;
        }
        .ss-row-order__btn + .ss-row-order__btn {
            border-left: 1px solid #dcdcde;
        }
        .ss-row-order__btn:hover:not(:disabled) {
            background: #f0f0f1;
            color: #1d2327;
        }
        .ss-row-order__btn:focus-visible {
            outline: 2px solid #2271b1;
            outline-offset: -2px;
            z-index: 1;
        }
        .ss-row-order__btn:active:not(:disabled) {
            background: #e2e4e7;
        }
        .ss-row-order__btn:disabled {
            color: #c3c4c7;
            cursor: default;
            background: transparent;
        }
        .ss-row-order__icon {
            display: block;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 6px solid currentColor;
        }
        .ss-row-order__btn--down .ss-row-order__icon {
            border-bottom: 0;
            border-top: 6px solid currentColor;
        }
        .ss-admin-row-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-shrink: 0;
            margin-left: auto;
        }
        .ss-admin-row-actions .button.button-small {
            min-height: 28px;
            line-height: 26px;
            padding: 0 10px;
        }
        .ss-admin-row-actions .button.button-small[data-ss-still-remove],
        .ss-admin-row-actions .button.button-small[data-ss-poster-remove],
        .ss-admin-row-actions .button.button-small[data-ss-credits-remove],
        .ss-admin-row-actions .button.button-small[data-ss-contact-remove] {
            color: #b32d2e;
            border-color: #dcdcde;
            background: #fff;
            box-shadow: none;
        }
        .ss-admin-row-actions .button.button-small[data-ss-still-remove]:hover,
        .ss-admin-row-actions .button.button-small[data-ss-poster-remove]:hover,
        .ss-admin-row-actions .button.button-small[data-ss-credits-remove]:hover,
        .ss-admin-row-actions .button.button-small[data-ss-contact-remove]:hover {
            color: #8a2424;
            background: #fcf0f1;
            border-color: #d63638;
        }
    </style>
    <?php
}