<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ExtendSiteIconImageSizesFeature extends FeatureAbstract
{
    protected $options = [
        'icons' => [
            // The classic favicon, displayed in the tabs.
            ['width' => 16, 'height' => 16],
            // WordPress already have this favicon.
            // Basic icon, also certain old but not too old Chrome versions mishandle ico.
            // ['width' => 32, 'height' => 32]
            // WordPress already have this favicon.
            // Old home screen for Android, MacBook Pro, iMac 27', Nexus 7 and other.
            // ['width' => 192, 'height' => 192]
        ]
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
        $siteIconId = get_option('site_icon');
        $siteIconMimeType = get_post_mime_type($siteIconId);

        foreach ($this->options['icons'] as $icon) {
            $width = $icon['width'];
            $height = $icon['width'];
            $icon = get_site_icon_url($icon['width']);

            if ($icon) {
                $metaTags[] = sprintf(
                    '<link rel="icon" type="%s" sizes="%dx%d" href="%s">',
                    esc_attr($siteIconMimeType),
                    esc_attr($width),
                    esc_attr($height),
                    esc_url($icon)
                );
            }
        }

        return $metaTags;
    }
}
