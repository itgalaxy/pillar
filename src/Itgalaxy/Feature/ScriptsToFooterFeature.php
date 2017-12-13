<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ScriptsToFooterFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('wp_enqueue_scripts', [$this, 'scriptsToFooter']);
    }

    /**
     * Remove all actions which output scripts in head.
     *
     * @return void
     */
    public function scriptsToFooter()
    {
        if (has_action('wp_head', 'wp_print_scripts') !== false) {
            remove_action('wp_head', 'wp_print_scripts');
        }

        if (has_action('wp_head', 'wp_print_head_scripts') !== false) {
            remove_action('wp_head', 'wp_print_head_scripts', 9);
        }

        if (has_action('wp_head', 'wp_enqueue_scripts') !== false) {
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
        }
    }
}
