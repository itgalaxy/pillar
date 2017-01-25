<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\JpegQualityFeature;

class JpegQualityFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(JpegQualityFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(JpegQualityFeature::class);

        parent::tearDown();
    }

    public function testJpegQuality()
    {
        $quality = apply_filters('jpeg_quality', 0);

        $this->assertTrue($quality === 100);
    }
}
