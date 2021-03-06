<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ExtendSiteIconImageSizesFeature extends FeatureAbstract
{
    protected $options = [
        'icons' => [
            // The classic favicon, displayed in the tabs.
            ['width' => 16, 'height' => 16],
            // Basic icon, also certain old but not too old Chrome versions mishandle ico.
            ['width' => 32, 'height' => 32],
            // Old home screen for Android, MacBook Pro, iMac 27', Nexus 7 and other.
            ['width' => 192, 'height' => 192]
        ],
        'noDefault' => true
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('site_icon_image_sizes', [$this, 'siteIconImageSizes']);
        add_filter('site_icon_meta_tags', [$this, 'siteIconMetaTags']);
    }

    public function siteIconImageSizes($sizes)
    {
        if ($this->options['noDefault']) {
            $index = array_search([192, 32], $sizes);

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

                if ($strpos($metaTag, 'rel="icon"') !== false
                    && ($strpos($metaTag, 'sizes="32x32"') !== false || $strpos($metaTag, 'sizes="192x192"') !== false)
                ) {
                    unset($metaTags[$index]);
                }
            }
        }

        $siteIconId = get_option('site_icon');

        if (!$siteIconId) {
            return $metaTags;
        }

        $siteIconMimeType = get_post_mime_type($siteIconId);

        foreach ($this->options['icons'] as $icon) {
            $width = $icon['width'];
            $height = $icon['height'];
            $siteIconURL = get_site_icon_url($icon['width']);

            if ($siteIconURL) {
                $metaTags[] = sprintf(
                    '<link rel="icon" type="%s" sizes="%dx%d" href="%s">',
                    esc_attr($siteIconMimeType),
                    esc_attr($width),
                    esc_attr($height),
                    esc_url($siteIconURL)
                );
            }
        }

        return $metaTags;
    }
}
