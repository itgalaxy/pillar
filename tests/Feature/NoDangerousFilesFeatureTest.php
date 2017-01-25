<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoDangerousFilesFeature;

class NoDangerousFilesFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoDangerousFilesFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(NoDangerousFilesFeature::class);

        parent::tearDown();
    }

    public function test()
    {
       // Nothing
    }
}
