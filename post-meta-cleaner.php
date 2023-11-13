<?php
/*
    Plugin Name: Post Meta Cleaner
    Description: A WordPress plugin for removing duplicate post meta.
    Version: 1.0.0
    Requires PHP: 8.2
    Text Domain: post-meta-cleaner
    Author: Seb Kay
    Author URI: http://sebkay.com
    WC requires at least: 6.0.0
    WC tested up to: 6.4.1
*/

defined('ABSPATH') or exit;

define('PMC_PLUGIN_NAME', 'Post Meta Cleaner');
define('PMC_PLUGIN_SLUG', 'pmc');
define('PMC_DIR_PATH', plugin_dir_path(__FILE__));
define('PMC_WP_ROOT', PMC_DIR_PATH.'/../../../');
define('PMC_LOGS', PMC_DIR_PATH.'/pmc-logs');

if (file_exists(PMC_DIR_PATH.'/vendor/autoload.php')) {
    require_once PMC_DIR_PATH.'/vendor/autoload.php';
} else {
    return;
}

function pmc_plugin()
{
    return PMC\Plugin::instance();
}

pmc_plugin()->run();
