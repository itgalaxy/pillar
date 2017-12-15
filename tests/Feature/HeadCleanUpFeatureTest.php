<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\HeadCleanUpFeature;

class HeadCleanUpFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(HeadCleanUpFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(HeadCleanUpFeature::class);

        parent::tearDown();
    }

    public function testHead()
    {
        $this->assertFalse(has_action('wp_head', 'rsd_link'));
        $this->assertFalse(has_action('wp_head', 'wlwmanifest_link'));
        $this->assertFalse(has_action('wp_head', 'adjacent_posts_rel_link'));
        $this->assertFalse(has_action('wp_head', 'wp_generator'));
        $this->assertFalse(has_action('wp_head', 'wp_shortlink_wp_head'));

        $this->assertFalse(has_action('wp_head', 'wp_oembed_add_discovery_links'));
        $this->assertFalse(has_action('wp_head', 'wp_oembed_add_host_js'));
    }
}
