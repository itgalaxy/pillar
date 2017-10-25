<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class AppleIconsAndMetaFeature extends FeatureAbstract
{
    protected $options = [
        'icons' => [
            // Displays meaning:
            // @1x - non-Retina
            // @2x - Retina
            // @3x - Retina HD
            //
            // iPhone with @1x display and iPod Touch
            ['width' => 57, 'height' => 57],
            // For non-retina iPhone with iOS7.
            ['width' => 60, 'height' => 60],
            // iPad and iPad mini with @1x display running iOS ≤ 6
            ['width' => 72, 'height' => 72],
            // iPad and iPad mini with @1x display running iOS ≥ 7
            ['width' => 76, 'height' => 76],
            // iPhone with @2x display running iOS ≤ 6
            ['width' => 114, 'height' => 114],
            // iPhone with @2x and @3x display running iOS ≥ 7
            ['width' => 120, 'height' => 120],
            // iPad and iPad mini with @2x display running iOS ≤ 6
            ['width' => 144, 'height' => 144],
            // iPad and iPad mini with @2x display running iOS 7
            ['width' => 152, 'height' => 152],
            // iPad and iPad mini with @2x display running iOS 8
            ['width' => 180, 'height' => 180],
            // Touch icon for iOS 2.0+ and Android 2.1+
            ['width' => 180, 'height' => 180, 'fallback' => true]
        ],
        'noDefault' => true,
        // Make your web app chrome-less and provide the default iOS app view.
        'appleMobileWebAppCapable' => 'yes',
        //  Control the color scheme of the default view
        'appleMobileWebAppStatusBarStyle' => 'black-translucent',
        // You can use apple-mobile-web-app-title to add a specific sites name for the Home Screen icon.
        'appleMobileWebAppTitle' => null
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

        if ($this->options['noDefault']) {
            $index = array_search(180, $sizes);

            if ($index !== null) {
                unset($sizes[$index]);
            }
        }

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

                if ($strpos($metaTag, 'rel="apple-touch-icon-precomposed"') !== false) {
                    unset($metaTags[$index]);
                }
            }
        }

        $siteIconId = get_option('site_icon');

        if (!$siteIconId) {
            return;
        }

        $siteIconMimeType = get_post_mime_type($siteIconId);

        foreach ($this->options['icons'] as $icon) {
            if (!empty($icon['fallback'])) {
                continue;
            }

            $siteIconURL = get_site_icon_url($icon['width']);

            if ($siteIconURL) {
                $metaTags[] = sprintf(
                    '<link rel="apple-touch-icon" type="%s" sizes="%dx%d" href="%s">',
                    esc_attr($siteIconMimeType),
                    esc_attr($icon['width']),
                    esc_attr($icon['height']),
                    esc_url($siteIconURL)
                );
            }
        }

        $fallbackIconResult = array_filter($this->options['icons'], function ($icon) {
            return !empty($icon['fallback']) && $icon['fallback'] == true;
        });

        $fallbackIcon = is_array($fallbackIconResult) && !empty($fallbackIconResult)
            ? current($fallbackIconResult)
            : null;

        if (!empty($fallbackIcon) && !empty($fallbackIcon['width'])) {
            $siteIconURLFallback = get_site_icon_url($fallbackIcon['width']);

            if ($siteIconURLFallback) {
                $metaTags[] = sprintf(
                    '<link rel="apple-touch-icon" type="%s" href="%s">',
                    esc_attr($siteIconMimeType),
                    esc_url($siteIconURLFallback)
                );
            }
        }

        if (!empty($this->options['appleMobileWebAppCapable'])) {
            $metaTags[] = sprintf(
                '<meta name="apple-mobile-web-app-capable" content="%s">',
                esc_attr($this->options['appleMobileWebAppCapable'])
            );
        }

        if (!empty($this->options['appleMobileWebAppStatusBarStyle'])) {
            $metaTags[] = sprintf(
                '<meta name="apple-mobile-web-app-status-bar-style" content="%s">',
                esc_attr($this->options['appleMobileWebAppStatusBarStyle'])
            );
        }

        $appleMobileWebAppTitleValue = !empty($this->options['appleMobileWebAppTitle'])
            ? $this->options['appleMobileWebAppTitle']
            : esc_attr(get_bloginfo('name'));

        $metaTags[] = sprintf(
            '<meta name="apple-mobile-web-app-title" content="%s">',
            esc_attr($appleMobileWebAppTitleValue)
        );

        return $metaTags;
    }
}
