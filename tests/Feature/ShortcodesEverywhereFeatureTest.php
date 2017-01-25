<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\ShortcodesEverywhereFeature;

class ShortcodesEverywhereFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(ShortcodesEverywhereFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(ShortcodesEverywhereFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Need tests
    }
}
