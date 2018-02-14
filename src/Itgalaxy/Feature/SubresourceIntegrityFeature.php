<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class SubresourceIntegrityFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('script_loader_tag', [$this, 'add_integrity_attribute'], 10, 2);
        add_filter('style_loader_tag', [$this, 'add_integrity_attribute'], 10, 2);
    }

    public function add_integrity_attribute($html, $handle) {
        if (strpos($html, 'integrity')) {
            return $html;
        }

        $currentFilter = current_filter();

        if ($currentFilter == 'script_loader_tag') {
            global $wp_scripts;

            $meta = $wp_scripts->registered[$handle];
            $src = $meta->src;
            $tag = 'script';
        } else if ($currentFilter == 'style_loader_tag') {
            global $wp_styles;

            $meta = $wp_styles->registered[$handle];
            $src = $meta->src;
            $tag = 'link';
        } else {
            return $html;
        }

        $parsedSrcURL = parse_url($src);
        $parsedHomeURL = parse_url(get_home_url());

        if (isset($parsedSrcURL['scheme']) && $parsedSrcURL['scheme'] !== $parsedHomeURL['scheme']) {
            return $html;
        }

        if (isset($parsedSrcURL['host']) && $parsedSrcURL['host'] !== $parsedHomeURL['host']) {
            return $html;
        }

        if (!function_exists('get_home_path')) {
            include_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $homePath = get_home_path();

        if (!$homePath || count($homePath) === 0) {
            return $html;
        }

        $path = untrailingslashit($homePath) . $src;

        if (!file_exists($path)) {
            return $html;
        }

        $data = file_get_contents($path);
        $key = 'pillar_subresource_integrity_key_' . md5($data);
        $integrity = get_transient($key);

        if (!$integrity) {
            $integrity = base64_encode(hash("sha512", $data, true));

            set_transient($key, $integrity, '', YEAR_IN_SECONDS);
        }

        return str_replace(
            $tag . ' ',
            $tag . ' integrity="sha512-' . $integrity . '" ',
            $html
        );
    }
}
