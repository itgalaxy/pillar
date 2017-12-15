<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\MicroformatFeature;

class MicroformatFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(MicroformatFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(MicroformatFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
