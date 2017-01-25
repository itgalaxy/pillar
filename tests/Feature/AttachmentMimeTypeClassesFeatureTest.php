<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\AttachmentMimeTypeClassesFeature;

class AttachmentMimeTypeClassesFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(AttachmentMimeTypeClassesFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(AttachmentMimeTypeClassesFeature::class);

        parent::tearDown();
    }

    public function test()
    {
        // Nothing
    }
}
