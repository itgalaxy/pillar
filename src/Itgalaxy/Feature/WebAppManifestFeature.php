<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class WebAppManifestFeature extends FeatureAbstract
{
    protected $options = [
        'queryVar' => 'web-app-manifest',
        'filename' => 'web-app-manifest.json',
        'icons' => [
            ['width' => 36, 'height' => 36],
            ['width' => 48, 'height' => 48],
            ['width' => 72, 'height' => 72],
            ['width' => 96, 'height' => 96],
            ['width' => 144, 'height' => 144],
            ['width' => 192, 'height' => 192],
            ['width' => 256, 'height' => 256],
            ['width' => 384, 'height' => 384],
            ['width' => 512, 'height' => 512]
        ],
        'shortName' => null,
        'description' => null,
        'display' => 'standalone',
        'orientation' => 'any',
        'backgroundColor' => null,
        // Meta `theme-color` meta extension is supported by Chrome 39+ for Android Lollipop and Firefox OS 2.1+.
        'themeColor' => null
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('query_vars', [$this, 'queryVars']);
        add_filter('rewrite_rules_array', [$this, 'rewriteRulesArray']);
        add_action('parse_query', [$this, 'handle']);

        add_filter('site_icon_image_sizes', [$this, 'siteIconImageSizes']);
        add_filter('site_icon_meta_tags', [$this, 'siteIconMetaTags']);
    }

    public function queryVars($queryVars)
    {
        $queryVars[] = $this->options['queryVar'];

        return $queryVars;
    }

    public function rewriteRulesArray($rules)
    {
        $newRewriteRules = [];

        $newRewriteRules['^' . preg_quote($this->options['filename']) . '$'] = 'index.php?'
            . $this->options['queryVar']
            . '='
            . pathinfo($this->options['filename'], PATHINFO_FILENAME);

        return $newRewriteRules + $rules;
    }

    public function handle($query)
    {
        if (array_key_exists($this->options['queryVar'], $query->query_vars)) {
            header('Content-Type: application/manifest+json; charset=' . get_option('blog_charset'), true);

            $manifest = new \stdClass();

            $manifest->name = !empty($this->options['name'])
                ? $this->options['name']
                : get_bloginfo('name');

            $manifest->short_name = !empty($this->options['shortName'])
                ? $this->options['shortName']
                : get_bloginfo('name');

            $manifest->description = !empty($this->options['description'])
                ? $this->options['description']
                : get_bloginfo('description');

            if (!empty($this->options['display'])) {
                $manifest->display = $this->options['display'];
            }

            if (!empty($this->options['orientation'])) {
                $manifest->orientation = $this->options['orientation'];
            }

            $manifest->dir = is_rtl() ? 'rtl' : 'ltr';
            $manifest->lang = str_replace('_', '-', get_locale());

            $manifest->start_url = get_home_url(null, '?utm_source=web-app-manifest');

            $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

            if (!empty($this->options['backgroundColor'])) {
                $manifest->background_color = $strtolower($this->options['backgroundColor']);
            }

            if (!empty($this->options['themeColor'])) {
                $manifest->theme_color = $strtolower($this->options['themeColor']);
            }

            $manifest->icons = [];

            foreach ($this->options['icons'] as $icon) {
                $siteIconSrc = get_site_icon_url($icon['width']);
                $siteIconId = get_option('site_icon');
                $siteIconMimeType = get_post_mime_type($siteIconId);

                if ($siteIconSrc) {
                    array_push(
                        $manifest->icons,
                        [
                            'src' => $siteIconSrc,
                            'sizes' => $icon['width'] . 'x' . $icon['height'],
                            'type' => $siteIconMimeType
                        ]
                    );
                }
            }

            echo wp_json_encode($manifest);

            if (wp_doing_ajax()) {
                wp_die('', '', ['response' => null]);
                // escape ok
            } else {
                exit();
            }
        }
    }

    public function siteIconImageSizes($sizes)
    {
        $newSizes = [];

        foreach ($this->options['icons'] as $size) {
            if (empty($size['width']) || !is_int($size['width'])) {
                continue;
            }

            $newSizes[] = $size['width'];
        }

        return array_unique(array_merge($sizes, $newSizes));
    }

    public function siteIconMetaTags($metaTags)
    {
        if (!empty($this->options['themeColor'])) {
            $metaTags[] = sprintf(
                '<meta name="theme-color" content="%s">',
                esc_attr($this->options['themeColor'])
            );
        }

        $permalinkStructure = get_option('permalink_structure');
        $url = $permalinkStructure
            ? get_home_url(null, $this->options['filename'])
            : 'index.php?' . $this->options['queryVar'] . '=' . $this->options['filename'];

        $metaTags[] = sprintf(
            '<link rel="manifest" href="%s">',
            esc_url($url)
        );

        return $metaTags;
    }
}
