<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoDefaultWordpressStylesFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        if (has_filter('use_default_gallery_style', '__return_false') === false) {
            add_filter('use_default_gallery_style', '__return_false');
        }

        add_action('widgets_init', function () {
            global $wp_widget_factory;

            if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])
                && has_action('wp_head', [
                    $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
                    'recent_comments_style'
                ]) !== false
            ) {
                remove_action(
                    'wp_head',
                    [
                        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
                        'recent_comments_style'
                    ]
                );
            }
        });
    }
}
