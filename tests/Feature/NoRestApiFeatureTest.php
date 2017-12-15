<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoRestApiFeature;

class NoRestApiFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoRestApiFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(NoRestApiFeature::class);

        parent::tearDown();
    }

    public function testRemoveFromHeadAndHeaders()
    {
        $this->assertFalse(has_action('xmlrpc_rsd_apis', 'rest_output_rsd'));
        $this->assertFalse(has_action('wp_head', 'rest_output_link_wp_head'));
        $this->assertFalse(has_action('template_redirect', 'rest_output_link_header'));
    }

    public function testDisableJsonp()
    {
        $this->assertTrue(apply_filters('rest_jsonp_enabled', true) === false);
        $this->assertTrue(apply_filters('json_jsonp_enabled', true) === false);
    }

    public function testDisableRestInWpAPIVersion1()
    {
        $this->assertTrue(apply_filters('json_enabled', true) === false);
    }

    public function testDisableRestInWpAPIVersion2()
    {
        global $wp_version;

        FeatureFactory::unloadFeature(NoRestApiFeature::class);

        $oldVersion = $wp_version;

        $wp_version = '4.6.0';

        FeatureFactory::loadFeature(NoRestApiFeature::class);

        $this->assertTrue(apply_filters('rest_enabled', true) === false);

        $wp_version = $oldVersion;
    }
}
