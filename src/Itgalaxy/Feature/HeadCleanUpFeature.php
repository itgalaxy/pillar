<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class HeadCleanUpFeature extends FeatureAbstract
{
    protected $options = [
        'feedLinks' => true,
        'feedLinksExtra' => true,
        'rsdLink' => true,
        'wlwmanifestLink' => true,
        'adjacentPostsRelLink' => true,
        'wpGenerator' => true,
        'wpShortlink' => true,
        'indexRelLink' => true,
        'parentPostRelLink' => true,
        'startPostRelLink' => true,
        'restLink' => true
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $options = $this->options;

        // This will remove the standard feed links
        if ($options['feedLinks'] && has_action('wp_head', 'feed_links') !== false) {
            remove_action('wp_head', 'feed_links', 2);
        }

        // This will remove index link
        if ($options['feedLinksExtra'] && has_action('wp_head', 'feed_links_extra') !== false) {
            remove_action('wp_head', 'feed_links_extra', 3);
        }

        // This will remove Really Simple Discovery link from the header
        if ($options['rsdLink'] && has_action('wp_head', 'rsd_link') !== false) {
            remove_action('wp_head', 'rsd_link');
        }

        // This will remove wlwmanifest
        if ($options['wlwmanifestLink'] && has_action('wp_head', 'wlwmanifest_link') !== false) {
            remove_action('wp_head', 'wlwmanifest_link');
        }

        // This will remove the prev and next post link
        if ($options['adjacentPostsRelLink'] && has_action('wp_head', 'adjacent_posts_rel_link_wp_head') !== false) {
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        }

        // This will remove the WordPress generator tag
        if ($options['wpGenerator']) {
            if (has_action('wp_head', 'wp_generator') !== false) {
                remove_action('wp_head', 'wp_generator');
            }

            add_filter(
                'the_generator',
                function () {
                    return '';
                },
                0
            );
        }

        // This will remove shortlink for the page
        if ($options['wpShortlink'] && has_action('wp_head', 'wp_shortlink_wp_head') !== false) {
            remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        }

        $wordPressVersion = get_bloginfo('version');

        if (version_compare($wordPressVersion, '3.1', '<')) {
            if ($options['adjacentPostsRelLink'] && has_action('wp_head', 'adjacent_posts_rel_link') !== false) {
                remove_action('wp_head', 'adjacent_posts_rel_link');
            }
        }

        if (version_compare($wordPressVersion, '3.3', '<')) {
            // This will remove index link
            if ($options['indexRelLink'] && has_action('wp_head', 'index_rel_link') !== false) {
                remove_action('wp_head', 'index_rel_link');
            }

            // This will remove parent post link
            if ($options['parentPostRelLink'] && has_action('wp_head', 'parent_post_rel_link') !== false) {
                remove_action('wp_head', 'parent_post_rel_link', 10);
            }

            // This will remove start post link
            if ($options['startPostRelLink'] && has_action('wp_head', 'start_post_rel_link') !== false) {
                remove_action('wp_head', 'start_post_rel_link', 10);
            }
        }

        if ($options['restLink'] && has_action('wp_head', 'rest_output_link_wp_head') !== false) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }

        // Disable build-in oembed inside theme
        if (has_action('wp_head', 'wp_oembed_add_discovery_links') !== false) {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
        }

        if (has_action('wp_head', 'wp_oembed_add_host_js') !== false) {
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }
    }
}
