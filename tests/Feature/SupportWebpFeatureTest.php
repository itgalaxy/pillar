<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\SupportWebpFeature;

class SupportWebpFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(SupportWebpFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(SupportWebpFeature::class);

        parent::tearDown();
    }

    public function testSupportWebpPlugin()
    {
        $this->assertArraySubset(
            [
                'webp' => 'image/webp'
            ],
            get_allowed_mime_types()
        );
    }
}
