<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\ScriptsToFooterFeature;

class ScriptsToFooterFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(ScriptsToFooterFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(ScriptsToFooterFeature::class);

        parent::tearDown();
    }

    public function test()
    {
       // Nothing
    }
}
