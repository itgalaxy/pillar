<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Pillar
 */

define('PHPUNIT_RUNNING', 1);
define('PLUGIN_DIR_TESTDATA', __DIR__ . '/data');
define('FIXTURE_DIR', __DIR__ . '/Fixture');

// Get our tests directory.
$_tests_dir = getenv('WP_TESTS_DIR') ? getenv('WP_TESTS_DIR') : '/tmp/wordpress-tests-lib';

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_pillar_plugin()
{
    // Include the REST API main plugin file if we're using it so we can run endpoint tests.
    if (class_exists('WP_REST_Controller') && file_exists(WP_PLUGIN_DIR . '/rest-api/plugin.php')) {
        include WP_PLUGIN_DIR . '/rest-api/plugin.php';
    }

    include dirname(dirname(__FILE__)) . '/pillar.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_pillar_plugin');

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

if (!function_exists('wp_handle_upload_error')) {
    function wp_handle_upload_error(&$file, $message) {
        return [
            'error' => [
                'file' => $file,
                'message' => $message
            ]
        ];
    }
}
