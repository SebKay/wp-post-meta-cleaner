<?php

use Illuminate\Support\LazyCollection;

defined('ABSPATH') or exit;

/**
 * Create options page
 */
function pmc_create_options_page()
{
    if (! current_user_can('administrator')) {
        return;
    }

    add_submenu_page(
        'tools.php',
        PMC_PLUGIN_NAME,
        PMC_PLUGIN_NAME,
        'manage_options',
        'pmc-settings',
        fn () => include PMC_DIR_PATH.'/inc/options-content.php'
    );
}

add_action('admin_menu', 'pmc_create_options_page');

/**
 * Register custom options
 */
function pmc_register_options()
{
    register_setting('pmc-options', 'pmc_text_option');
    register_setting('pmc-options', 'pmc_radio_option');
    register_setting('pmc-options', 'pmc_select_option');
}

add_action('admin_init', 'pmc_register_options');

/**
 * Check if we're doing a settings page form request
 */
function pmc_is_settings_page_form_request(string $formField)
{
    global $pagenow;
    $page = $_GET['page'] ?? null;

    return $pagenow == 'tools.php' && $page == 'pmc-settings' && isset($_POST[$formField]);
}

/**
 * Handle form request to count duplicate meta
 */
function pmc_form_request_count_duplicate_post_meta()
{
    $fieldName = 'count_duplicate_meta';

    if (! pmc_is_settings_page_form_request($fieldName)) {
        return;
    }

    try {
        pmc_plugin()->calculateDuplicateMeta();
    } catch (Exception $e) {
        error_log($e);

        pmc_plugin()->logger()->general()->error($e);
    }

    wp_redirect(remove_query_arg($fieldName));
    exit;
}

add_action('admin_init', 'pmc_form_request_count_duplicate_post_meta');

/**
 * Handle form request to count duplicate meta
 */
function pmc_form_request_delete_duplicate_post_meta()
{
    $fieldName = 'delete_duplicate_meta';

    if (! pmc_is_settings_page_form_request($fieldName)) {
        return;
    }

    $rows = LazyCollection::make(get_option('pmc_multiple_meta_rows', []));

    if ($rows->isNotEmpty()) {
        try {
            $startTime = microtime(true);

            $rows->each(function ($row) {
                $deletableRows = pmc_plugin()->getDuplicateRows($row['post_id'], $row['meta_key']);

                if ($deletableRows->isNotEmpty()) {
                    pmc_plugin()->deleteDuplicateRows($deletableRows);
                }
            });

            $endTime = microtime(true);
            ray()->green('Deleted duplicate meta in '.($endTime - $startTime).' seconds');

            pmc_plugin()->calculateDuplicateMeta();
        } catch (Exception $e) {
            error_log($e);

            pmc_plugin()->logger()->general()->error($e);
        }
    }

    wp_redirect(remove_query_arg($fieldName));
    exit;
}

add_action('admin_init', 'pmc_form_request_delete_duplicate_post_meta');
