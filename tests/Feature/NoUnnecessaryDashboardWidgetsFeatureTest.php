<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoUnnecessaryDashboardWidgetsFeature;

class NoUnnecessaryDashboardWidgetsFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoUnnecessaryDashboardWidgetsFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(NoUnnecessaryDashboardWidgetsFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
