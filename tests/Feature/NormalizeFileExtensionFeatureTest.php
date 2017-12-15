<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NormalizeFileExtensionFeature;

class NormalizeFileExtensionFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NormalizeFileExtensionFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(NormalizeFileExtensionFeature::class);

        parent::tearDown();
    }

    public function testShouldRenameFileExtension()
    {
        $this->assertEquals('jpg', pathinfo(sanitize_file_name('foo.jpeg'))['extension']);
    }

    public function testShouldNotRenameFileExtension()
    {
        $this->assertEquals('png', pathinfo(sanitize_file_name('foo.png'))['extension']);
    }
}
