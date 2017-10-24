<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class WindowsIconsAndManifestFeature extends FeatureAbstract
{
    protected $options = [
        'queryVar' => 'browserconfig-xml',
        'filename' => 'browserconfig.xml',
        'icons' => [
            ['width' => 144, 'height' => 144, 'fallback' => true],
            // Image size 70x70
            ['width' => 128, 'height' => 128, 'inManifest' => true, 'name' => 'square70x70logo'],
            // Image size 150x150
            ['width' => 270, 'height' => 270, 'inManifest' => true, 'name' => 'square150x150logo'],
            // Image size 310x150
            // Impossible, all icons should be square in WordPress :sob:
            ['width' => 310, 'height' => 310, 'inManifest' => true, 'name' => 'wide310x150logo'],
            // Image size 310x310
            // Max size 512px for icon
            ['width' => 310, 'height' => 310, 'inManifest' => true, 'name' => 'square310x310logo']
        ],
        'noDefault' => true,
        'msapplicationTileColor' => null,
        'msapplicationTooltip' => null,
        'msapplicationStartUrl' => null,
        'msapplicationNavButtonColor' => null
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        if (class_exists('SimpleXMLElement')) {
            add_filter('query_vars', [$this, 'queryVars']);
            add_filter('rewrite_rules_array', [$this, 'rewriteRulesArray']);
            add_action('parse_query', [$this, 'handle']);
        }

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
            header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><browserconfig/>');
            $msapplicationNode = $xml->addChild('msapplication');
            $tileNode = $msapplicationNode->addChild('tile');

            foreach ($this->options['icons'] as $icon) {
                if (empty($icon['inManifest'])) {
                    continue;
                }

                $siteIconURL = get_site_icon_url($icon['width']);

                if ($siteIconURL) {
                    $iconNode = $tileNode->addChild($icon['name']);
                    $iconNode->addAttribute('src', $siteIconURL);
                }
            }


            if (!empty($this->options['msapplicationTileColor'])) {
                $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

                $tileNode->addChild('TileColor', $strtolower($this->options['msapplicationTileColor']));
            }

            echo $xml->asXML(); // escape ok

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
        if ($this->options['noDefault']) {
            $index = array_search(270, $sizes);

            if ($index !== null) {
                unset($sizes[$index]);
            }
        }

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
        if ($this->options['noDefault']) {
            foreach ($metaTags as $index => $metaTag) {
                $strpos = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';

                if ($strpos($metaTag, 'name="msapplication-TileImage"') !== false) {
                    unset($metaTags[$index]);
                }
            }
        }

        $permalinkStructure = get_option('permalink_structure');

        if (!empty($this->options['msapplicationTileColor'])) {
            $metaTags[] = sprintf(
                '<meta name="msapplication-TileColor" content="%s">',
                esc_attr($this->options['msapplicationTileColor'])
            );
        }

        if (!empty($this->options['icons'])) {
            $fallbackIconResult = array_filter(
                $this->options['icons'],
                function ($icon) {
                    return !empty($icon['fallback']) && $icon['fallback'] == true;
                }
            );
            $fallbackIcon = is_array($fallbackIconResult) && !empty($fallbackIconResult)
                ? current($fallbackIconResult)
                : null;

            if (!empty($fallbackIcon) && !empty($fallbackIcon['width'])) {
                $siteIconURLFallback = get_site_icon_url($fallbackIcon['width']);

                if ($siteIconURLFallback) {
                    // The msapplication-TileImage value is only supported in Microsoft Edge on Windows 10
                    // The msapplication-TileImage metadata is supported as of Windows 8
                    $metaTags[] = sprintf(
                        '<meta name="msapplication-TileImage" content="%s">',
                        esc_url($siteIconURLFallback)
                    );
                }
            }
        }

        $url = $permalinkStructure
            ? get_home_url(null, $this->options['filename'])
            : 'index.php?' . $this->options['queryVar'] . '=' . pathinfo($this->options['filename'], PATHINFO_FILENAME);

        $metaTags[] = sprintf(
            '<meta name="msapplication-config" content="%s">',
            esc_url($url)
        );

        if (!empty($this->options['msapplicationNavButtonColor'])) {
            $metaTags[] = sprintf(
                '<meta name="msapplication-navbutton-color" content="%s">',
                esc_attr($this->options['msapplicationNavButtonColor'])
            );
        }

        $msapplicationTooltipValue = !empty($this->options['msapplicationTooltip'])
            ? $this->options['msapplicationTooltip']
            : esc_attr(get_bloginfo('description'));

        $metaTags[] = sprintf(
            '<meta name="msapplication-tooltip" content="%s">',
            esc_attr($msapplicationTooltipValue)
        );

        $msapplicationStarturlValue = !empty($this->options['msapplicationStartUrl'])
            ? $this->options['msapplicationStartUrl']
            : get_home_url(null, '/?utm_source=pinned');

        $metaTags[] = sprintf(
            '<meta name="msapplication-starturl" content="%s">',
            esc_url($msapplicationStarturlValue)
        );

        return $metaTags;
    }
}
