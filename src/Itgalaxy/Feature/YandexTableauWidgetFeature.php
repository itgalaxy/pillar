<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class YandexTableauWidgetFeature extends FeatureAbstract
{
    protected $options = [
        'queryVar' => 'yandex-tableau-widget',
        'filename' => 'yandex-tableau-widget.json',
        'icons' => [
            ['width' => 120, 'height' => 120]
        ],
        'yandexManifestColor' => null
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

    public function activation()
    {
        flush_rewrite_rules();
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
            header('Content-Type: application/json; charset=' . get_option('blog_charset'), true);

            $manifest = new \stdClass();
            $manifest->api_version = 4;
            $manifest->layout = new \stdClass();

            $icon = current($this->options['icons']);

            $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

            if (!empty($icon['width'])) {
                $siteIconURL = get_site_icon_url($icon['width']);

                if ($siteIconURL) {
                    $ext = $strtolower(pathinfo($siteIconURL, PATHINFO_EXTENSION));

                    if ($ext == 'png') {
                        $manifest->layout->logo = $siteIconURL;
                    }
                }
            }

            if (!empty($this->options['yandexManifestColor'])) {
                $manifest->layout->color = $strtolower($this->options['yandexManifestColor']);
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
        $permalinkStructure = get_option('permalink_structure');

        $url = $permalinkStructure
            ? get_home_url(null, $this->options['filename'])
            : 'index.php?' . $this->options['queryVar'] . '=' . $this->options['filename'];

        $metaTags[] = sprintf(
            '<link rel="yandex-tableau-widget" href="%s">',
            esc_url($url)
        );

        return $metaTags;
    }
}
