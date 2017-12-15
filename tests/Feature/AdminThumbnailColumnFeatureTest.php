<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\AdminThumbnailColumnFeature;

class AdminThumbnailColumnFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(AdminThumbnailColumnFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(AdminThumbnailColumnFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
