<?php

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
