<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\SupportSvgFeature;

class SupportSvgFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(SupportSvgFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(SupportSvgFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
