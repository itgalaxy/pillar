<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\HeadCleanUpFeature;

class FeatureFactoryTest extends \WP_UnitTestCase
{
    public function testLoad()
    {
        $features = [
            'pillar-admin-thumbnail-column',
            'pillar-aria',
            'pillar-attachment-mime-type-classes',
            'pillar-convert-bmp-to-jpg',
            'pillar-cookie',
            'pillar-head-clean-up',
            'pillar-htmlmin',
            'pillar-jpeg-quality',
            'pillar-markup-wrapper',
            'pillar-max-resolution-upload-image',
            'pillar-microformat',
            'pillar-no-assets-versioning',
            'pillar-no-dangerous-files',
            'pillar-no-default-wordpress-styles',
            'pillar-no-emoji',
            'pillar-no-rest-api',
            'pillar-no-self-pingback',
            'pillar-no-unnecessary-dashboard-widgets',
            'pillar-no-wordpress-logo',
            'pillar-no-xmlrpc',
            'pillar-no-xmlrpc-pingback',
            'pillar-normalize-file-extension',
            'pillar-normalize-upload-file-name',
            'pillar-relative-urls',
            'pillar-scripts-to-footer',
            'pillar-shortcodes-everywhere',
            'pillar-support-svg',
            'pillar-support-webp'
        ];

        foreach ($features as $feature) {
            add_theme_support($feature);
        }

        FeatureFactory::loadFeatures();

        $expectedFeatures = array_map(function ($feature) {
            return '\\Itgalaxy\\Pillar\\Feature\\'
                . $this->dashesToCamelCase(str_replace('pillar-', '', $feature))
                . 'Feature';
        }, $features);

        $this->assertEquals($expectedFeatures, array_keys(FeatureFactory::getFeatures()));

        foreach ($expectedFeatures as $expectedFeature) {
            remove_theme_support($expectedFeature);
        }
    }

    public function testDoubleUnloadFeature()
    {
        $this->expectException(\Exception::class);

        FeatureFactory::loadFeature(HeadCleanUpFeature::class);
        FeatureFactory::unloadFeature(HeadCleanUpFeature::class);
        FeatureFactory::unloadFeature(HeadCleanUpFeature::class);
    }

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = true)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}
