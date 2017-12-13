<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoEmojiFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        if (has_action('wp_head', 'print_emoji_detection_script') !== false) {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
        }

        if (has_action('admin_print_scripts', 'print_emoji_detection_script') !== false) {
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
        }

        if (has_action('wp_print_styles', 'print_emoji_styles') !== false) {
            remove_action('wp_print_styles', 'print_emoji_styles');
        }

        if (has_action('admin_print_styles', 'print_emoji_styles') !== false) {
            remove_action('admin_print_styles', 'print_emoji_styles');
        }

        if (has_filter('the_content_feed', 'wp_staticize_emoji') !== false) {
            remove_filter('the_content_feed', 'wp_staticize_emoji');
        }

        if (has_filter('comment_text_rss', 'wp_staticize_emoji') !== false) {
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
        }

        if (has_filter('wp_mail', 'wp_staticize_emoji_for_email') !== false) {
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        }

        add_filter('tiny_mce_plugins', function ($plugins) {
            if (is_array($plugins)) {
                return array_diff($plugins, ['wpemoji']);
            }

            return [];
        });

        if (has_filter('emoji_svg_url', '__return_false') === false) {
            add_filter('emoji_svg_url', '__return_false');
        }
    }
}
