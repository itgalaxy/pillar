<?php
/**
 *
 * @package   Pillar
 * @author    Itgalaxy <development@itgalaxy.company>
 * @license   MIT
 * @link      https://itgalaxy.company
 * @copyright 2017 Itgalaxy
 *
 * Plugin Name: Pillar
 * Plugin URI:  https://github.com/itgalaxy/pillar
 * Description: Collection WordPress mini plugins (theme features) to apply theme-agnostic modifications.
 * Version:     1.0.0
 * Author:      Itgalaxy
 * Author URI:  https://itgalaxy.company
 * Text Domain: pillar
 * License:     MIT
 * License URI: https://github.com/itgalaxy/pillar/blob/master/LICENSE
 * Domain Path: /languages
 */

namespace Itgalaxy\Pillar;

use Itgalaxy\Pillar\Base\FeatureFactory;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    exit();
}

/*
 * Require Composer autoloader if installed on it's own
 */
if (file_exists($autoloader = __DIR__ . '/vendor/autoload.php')) {
    include_once $autoloader;
}

$featureFactory = new FeatureFactory();

add_action('after_setup_theme', function () use ($featureFactory) {
    $featureFactory->loadFeatures();
}, PHP_INT_MAX);

register_activation_hook(__FILE__, function () use ($featureFactory) {
    add_option($featureFactory->getOptionName(), ['action' => 'activation']);
});

register_deactivation_hook(__FILE__, function () use ($featureFactory) {
    delete_option($featureFactory->getOptionName());
});
