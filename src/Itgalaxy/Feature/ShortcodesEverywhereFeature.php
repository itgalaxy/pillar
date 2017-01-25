<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ShortcodesEverywhereFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        // In post excerpts (automatic)
        // Todo Check is the shortcode already executed
        add_filter('wp_trim_excerpt', [$this, 'handleShortcodeInTrimExcerpt'], 10, 2);
        // In post excerpts (manual)
        add_filter('get_the_excerpt', 'do_shortcode');
        // In posts titles
        add_filter('the_title', 'do_shortcode');
        // In titles pages in browser title bar
        add_filter('single_post_title', 'do_shortcode');
        // In titles in browser title bar
        add_filter('wp_title', 'do_shortcode');
        // In widgets text
        add_filter('widget_text', 'shortcode_unautop');
        add_filter('widget_text', 'do_shortcode');
        // In widgets titles
        add_filter('widget_title', 'do_shortcode');
        // In site title and description
        add_filter('bloginfo', 'do_shortcode');
        // Todo In post/page custom fields
    }

    public function handleShortcodeInTrimExcerpt($text, $rawExcerpt)
    {
        if ($rawExcerpt != '') {
            return do_shortcode($rawExcerpt);
        }

        return $text;
    }
}
