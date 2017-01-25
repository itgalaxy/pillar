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
        'startPostRelLink' => true
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
        if (!empty($options['feedLinks'])) {
            remove_action('wp_head', 'feed_links', 2);
        }

        // This will remove index link
        if (!empty($options['feedLinksExtra'])) {
            remove_action('wp_head', 'feed_links_extra', 3);
        }

        // This will remove Really Simple Discovery link from the header
        if (!empty($options['rsdLink'])) {
            remove_action('wp_head', 'rsd_link');
        }

        // This will remove wlwmanifest
        if (!empty($options['wlwmanifestLink'])) {
            remove_action('wp_head', 'wlwmanifest_link');
        }

        // This will remove the prev and next post link
        if (!empty($options['adjacentPostsRelLink'])) {
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
        }

        // This will remove the Wordpress generator tag
        if (!empty($options['wpGenerator'])) {
            remove_action('wp_head', 'wp_generator');
            add_filter('the_generator', function () {
                return '';
            });
        }

        // This will remove shortlink for the page
        if (!empty($options['wpShortlink'])) {
            remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        }

        global $wp_version;

        if (version_compare($wp_version, '3.1', '<')) {
            if (!empty($options['adjacentPostsRelLink'])) {
                remove_action('wp_head', 'adjacent_posts_rel_link');
            }
        }

        if (version_compare($wp_version, '3.3', '<')) {
            // This will remove index link
            if (!empty($options['indexRelLink'])) {
                remove_action('wp_head', 'index_rel_link');
            }

            // This will remove parent post link
            if (!empty($options['parentPostRelLink'])) {
                remove_action('wp_head', 'parent_post_rel_link', 10);
            }

            // This will remove start post link
            if (!empty($options['startPostRelLink'])) {
                remove_action('wp_head', 'start_post_rel_link', 10);
            }
        }

        // Disable build-in oembed inside theme
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
}
