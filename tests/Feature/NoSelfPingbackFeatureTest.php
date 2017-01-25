<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoSelfPingbackFeature;

class NoSelfPingbackFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoSelfPingbackFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(NoSelfPingbackFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
