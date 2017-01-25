<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;
use Itgalaxy\Pillar\Util\URL;

class RelativeUrlsFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $strpos = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';

        if (isset($_GET['sitemap'])
            || (isset($_SERVER['REQUEST_URI']) && $strpos(wp_unslash($_SERVER['REQUEST_URI']), 'sitemap') !== false)
            || (isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']))
        ) {
            return;
        }

        if (is_admin()) {
            // Use relative url for attachments from editor
            if (defined('WP_CONTENT_URL') && mb_substr(WP_CONTENT_URL, 0, 1) != '/') {
                // Makes post content url relative
                add_filter('image_send_to_editor', [$this, 'URLtoRelativeInHTML'], 100);
                add_filter('media_send_to_editor', [$this, 'URLtoRelativeInHTML'], 100);
            }

            // Use relative url in content and excerpt
            add_filter('excerpt_save_pre', [$this, 'URLtoRelativeInContent'], 100);
            add_filter('content_save_pre', [$this, 'URLtoRelativeInContent'], 100);

            return;
        }

        // Use relative url only in templates
        add_filter('template_include', function ($template) {
            $permalink_structure = get_option('permalink_structure');

            // Filters from `wp-includes/link-template.php`
            add_filter('post_link', [$this, 'URLtoRelative'], 100);
            add_filter('post_type_link', [$this, 'URLtoRelative'], 100);
            add_filter('page_link', [$this, 'URLtoRelative'], 100);
            add_filter('attachment_link', [$this, 'URLtoRelative'], 100);
            add_filter('year_link', [$this, 'URLtoRelative'], 100);
            add_filter('month_link', [$this, 'URLtoRelative'], 100);
            add_filter('day_link', [$this, 'URLtoRelative'], 100);
            add_filter('feed_link', [$this, 'URLtoRelative'], 100);

            if ($permalink_structure != '') {
                add_filter('post_comments_feed_link', [$this, 'URLtoRelative'], 100);
                add_filter('author_feed_link', [$this, 'URLtoRelative'], 100);
                add_filter('category_feed_link', [$this, 'URLtoRelative'], 100);
                add_filter('tag_feed_link', [$this, 'URLtoRelative'], 100);
                add_filter('taxonomy_feed_link', [$this, 'URLtoRelative'], 100);
            }

            add_filter('get_edit_term_link', [$this, 'URLtoRelative'], 100);
            add_filter('search_link', [$this, 'URLtoRelative'], 100);
            add_filter('post_type_archive_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_edit_post_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_delete_post_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_edit_comment_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_edit_bookmark_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_edit_user_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_pagenum_link', [$this, 'URLtoRelative'], 100);
            add_filter('get_comments_pagenum_link', [$this, 'URLtoRelative'], 100);
            add_filter('includes_url', [$this, 'URLtoRelative'], 100);
            add_filter('content_url', [$this, 'URLtoRelative'], 100);
            add_filter('plugins_url', [$this, 'URLtoRelative'], 100);
            add_filter('user_admin_url', [$this, 'URLtoRelative'], 100);
            add_filter('user_dashboard_url', [$this, 'URLtoRelative'], 100);

            if (!is_user_admin() && !is_network_admin()) {
                add_filter('edit_profile_url', [$this, 'URLtoRelative'], 100);
            }

            add_filter('get_shortlink', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/taxonomy.php`
            add_filter('term_link', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/post.php`
            add_filter('wp_get_attachment_url', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/media.php`
            add_filter('wp_calculate_image_srcset', [$this, 'mapURLtoRelative'], 100);

            // Filters from `wp-includes/class.wp-scripts.php`
            add_filter('script_loader_src', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/class.wp-styles.php`
            add_filter('style_loader_src', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/author-template.php`
            add_filter('author_link', [$this, 'URLtoRelative'], 100);

            // Filters from `wp-includes/comment-template.php`
            add_filter('get_comment_author_url', [$this, 'URLtoRelative'], 100);

            return $template;
        });
    }

    public function URLtoRelative($url)
    {
        return URL::toRelative($url);
    }

    public function mapURLtoRelative($input)
    {
        if (!is_array($input)) {
            $input = [$input];
        }

        foreach ($input as $source => $src) {
            $input[$source]['url'] = $this->URLtoRelative($src['url']);
        }

        return $input;
    }

    public function URLtoRelativeInHTML($data)
    {
        if (!preg_match_all('/<(a|img)\s[^>]+>/i', $data, $matches)) {
            return $data;
        }

        // Todo ignore replace in `script`, `style`, `textarea` and `code` tags
        $dataRelativeImageSrc = preg_replace_callback('/<img(.*?)src=("|\')(.*?)("|\')(.*?)>/i', function ($matches) {
            return '<img'
                . $matches[1]
                . 'src='
                . $matches[2]
                . $this->URLtoRelative($matches[3])
                . $matches[4]
                . $matches[5]
                . '>';
        }, $data);

        if ($dataRelativeImageSrc === null) {
            return $data;
        }

        // Todo ignore replace in `script`, `style`, `textarea` and `code` tags
        $dataRelativeHref = preg_replace_callback('/<a(.*?)href=("|\')(.*?)("|\')(.*?)>/i', function ($matches) {
            return '<a'
                . $matches[1]
                . 'href='
                . $matches[2]
                . $this->URLtoRelative($matches[3])
                . $matches[4]
                . $matches[5]
                . '>';
        }, $dataRelativeImageSrc);

        if ($dataRelativeHref === null) {
            return $dataRelativeHref;
        }

        return $dataRelativeHref;
    }

    public function URLtoRelativeInContent($data)
    {
        return addslashes($this->URLtoRelativeInHTML(stripslashes($data)));
    }
}
