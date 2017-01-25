<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\AriaFeature;

class AriaFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(AriaFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(AriaFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
