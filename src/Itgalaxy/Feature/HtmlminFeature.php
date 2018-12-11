<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;
use Itgalaxy\Pillar\Util\HTML;
use Itgalaxy\Pillar\Util\Str;

class HtmlminFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        // Remove unnecessary html attributes from `script` and `style` tags
        add_filter('style_loader_tag', [$this, 'cleanStyleTag'], 100);
        add_filter('script_loader_tag', [$this, 'cleanScriptTag'], 100);

        // Remove self closing tag from `style` tag and newline between `style` tags
        add_filter('style_loader_tag', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);
        add_filter('style_loader_tag', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine'], 100);

        // Remove newline between script tags
        add_filter('script_loader_tag', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine'], 100);

        // Remove self closing tag from `img` tag
        add_filter('get_avatar', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);

        // Remove self closing tag from `input` tag
        add_filter('comment_id_fields', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);

        // Remove self closing tag from `img` tag
        add_filter('post_thumbnail_html', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);

        // Remove empty attributes from `img` tag
        add_filter('wp_get_attachment_image_attributes', [$this, 'removeEmptyAttributes'], 100);

        add_filter('previous_post_rel_link', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);
        add_filter('previous_post_rel_link', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine'], 100);

        add_filter('next_post_rel_link', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);
        add_filter('next_post_rel_link', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine'], 100);

        add_filter('oembed_discovery_links', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags'], 100);
        add_filter('oembed_discovery_links', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine']);

        add_filter('image_send_to_editor', [$this, 'removeSelfClosingTagsInHTML'], 100);
        add_filter('media_send_to_editor', [$this, 'removeSelfClosingTagsInHTML'], 100);

        add_filter('excerpt_save_pre', [$this, 'removeSelfClosingTagsInContent'], 100);
        add_filter('content_save_pre', [$this, 'removeSelfClosingTagsInContent'], 100);

        // Some plugins can remove default action.
        // Check action exists first.
        add_action('wp_head', function () {
            if (has_action('wp_head', '_wp_render_title_tag') !== false) {
                remove_action('wp_head', '_wp_render_title_tag', 1);
                add_action('wp_head', [$this, 'renderTitleTag'], 1);
            }

            if (has_action('wp_head', 'wp_resource_hints') !== false) {
                remove_action('wp_head', 'wp_resource_hints', 2);
                add_action('wp_head', [$this, 'renderResourceHints'], 2);
            }

            if (has_action('wp_head', 'feed_links') !== false) {
                remove_action('wp_head', 'feed_links', 2);
                add_action('wp_head', [$this, 'renderFeedLinks'], 2);
            }

            if (has_action('wp_head', 'feed_links_extra') !== false) {
                remove_action('wp_head', 'feed_links_extra', 3);
                add_action('wp_head', [$this, 'renderFeedLinksExtra'], 3);
            }

            if (has_action('wp_head', 'rsd_link') !== false) {
                remove_action('wp_head', 'rsd_link');
                add_action('wp_head', [$this, 'renderRsdLink']);
            }

            if (has_action('wp_head', 'wlwmanifest_link') !== false) {
                remove_action('wp_head', 'wlwmanifest_link');
                add_action('wp_head', [$this, 'renderWlwmanifestLink']);
            }

            if (has_action('wp_head', 'noindex') !== false) {
                remove_action('wp_head', 'noindex', 1);
                add_action('wp_head', [$this, 'renderNoindex'], 1);
            }

            if (has_action('wp_head', 'wp_generator') !== false) {
                remove_action('wp_head', 'wp_generator');
                add_action('wp_head', [$this, 'renderGenerator']);
            }

            if (class_exists('\\WPSEO_Frontend')) {
                // Remove ` Yoast SEO` version comment.
                add_filter('wpseo_hide_version', '__return_true');

                // Remove head Yoast SEO version info
                remove_action('wpseo_head', [\WPSEO_Frontend::get_instance(), 'debug_marker'], 2);

                // Remove self closing tag and newline <link rel="next"
                add_filter('wpseo_prev_rel_link', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags']);
                add_filter('wpseo_prev_rel_link', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine']);

                // Remove self closing tag and newline <link rel="next">
                add_filter('wpseo_next_rel_link', ['Itgalaxy\Pillar\Util\HTML', 'removeSelfClosingTags']);
                add_filter('wpseo_next_rel_link', ['Itgalaxy\Pillar\Util\Str', 'removeNewLine']);
            } elseif (has_action('wp_head', 'rel_canonical') !== false) {
                // Remove newline and self closing tag after <link rel="canonical">
                remove_action('wp_head', 'rel_canonical');
                add_action('wp_head', [$this, 'renderRelCanonical']);
            }

            if (has_action('wp_head', 'wp_shortlink_wp_head') !== false) {
                remove_action('wp_head', 'wp_shortlink_wp_head', 10);
                add_action('wp_head', [$this, 'renderShortlinkWpHead'], 10, 0);
            }

            if (has_action('wp_head', 'wp_site_icon') !== false) {
                remove_action('wp_head', 'wp_site_icon', 99);
                add_action('wp_head', [$this, 'renderSiteIcon'], 99);
            }

            if (has_action('login_head', 'wp_site_icon') !== false) {
                remove_action('login_head', 'wp_site_icon', 99);
                add_action('login_head', [$this, 'renderSiteIcon'], 99);
            }

            if (has_action('admin_head', 'wp_site_icon') !== false) {
                remove_action('admin_head', 'wp_site_icon', 99);
                add_action('admin_head', [$this, 'renderSiteIcon'], 99);
            }

            if (has_action('wp_head', 'wp_no_robots') !== false) {
                remove_action('wp_head', 'wp_no_robots');
                add_action('wp_head', [$this, 'renderNoRobots']);
            }

            if (has_action('wp_head', 'rest_output_link_wp_head') !== false) {
                remove_action('wp_head', 'rest_output_link_wp_head', 10);
                add_action('wp_head', [$this, 'renderRestOutputLinkWpHead'], 10, 0);
            }
        }, 0);

        // Remove `Stream` plugin HTML comment
        add_filter('wp_stream_frontend_indicator', '__return_false');
    }

    /**
     * Clean up output of stylesheet <link> tags.
     *
     * @param string $html Link tag.
     *
     * @return string Cleaned link tag.
     */
    public function cleanStyleTag($html)
    {
        $html = str_replace(" type='text/css'", '', $html);
        $html = str_replace(" media='all'", '', $html);

        return $html;
    }

    /**
     * Clean up output of <script> tags.
     *
     * @param string $html Script tag.
     *
     * @return string Cleaned script tag.
     */
    public function cleanScriptTag($html)
    {
        $html = str_replace(" type='text/javascript'", '', $html);

        return $html;
    }

    /**
     * Clean up output from empty html attributes.
     *
     * @param array $attributes Massive of html attributes.
     *
     * @return array Purified attributes.
     */
    public function removeEmptyAttributes($attributes)
    {
        $purifiedAttributes = [];

        foreach ($attributes as $attribute => $value) {
            if ($attribute != 'alt'
                && (empty($attribute) || strlen(trim($value)) == 0)
            ) {
                continue;
            }

            $purifiedAttributes[$attribute] = $value;
        }

        return $purifiedAttributes;
    }

    public function renderTitleTag()
    {
        echo Str::removeNewLine($this->getEcho('_wp_render_title_tag'));
    }

    public function renderResourceHints()
    {
        $output = $this->getEcho('wp_resource_hints');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderFeedLinks()
    {
        $output = $this->getEcho('feed_links');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderFeedLinksExtra()
    {
        $output = $this->getEcho('feed_links_extra');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderRsdLink()
    {
        $output = $this->getEcho('rsd_link');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderWlwmanifestLink()
    {
        $output = $this->getEcho('wlwmanifest_link');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderNoindex()
    {
        $output = $this->getEcho('noindex');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderGenerator()
    {
        echo Str::removeNewLine($this->getEcho('wp_generator'));
    }

    public function renderRelCanonical()
    {
        $output = $this->getEcho('rel_canonical');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderShortlinkWpHead()
    {
        $output = $this->getEcho('wp_shortlink_wp_head');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderSiteIcon()
    {
        $output = $this->getEcho('wp_site_icon');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderNoRobots()
    {
        $output = $this->getEcho('wp_no_robots');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function renderRestOutputLinkWpHead()
    {
        $output = $this->getEcho('rest_output_link_wp_head');
        $output = Str::removeNewLine($output);
        $output = HTML::removeSelfClosingTags($output);

        echo $output;
    }

    public function removeSelfClosingTagsInHTML($data)
    {
        $newData = preg_replace_callback('/(<[^>]+?)\s*?\/>/', function ($matches) {
            return $matches[1] . '>';
        }, $data);

        if ($newData === null) {
            return $data;
        }

        return $newData;
    }

    public function removeSelfClosingTagsInContent($data)
    {
        return addslashes($this->removeSelfClosingTagsInHTML(stripslashes($data)));
    }

    private function getEcho($callable, $args = [])
    {
        ob_start();

        call_user_func_array($callable, $args);

        return ob_get_clean();
    }
}
