<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoWordpressNavbarLogoFeature;

class NoWordpressNavbarLogoFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoWordpressNavbarLogoFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(NoWordpressNavbarLogoFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Need testing
    }
}
