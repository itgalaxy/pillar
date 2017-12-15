<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NormalizeUploadFileNameFeature;

class NormalizeUploadFileNameFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NormalizeUploadFileNameFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(NormalizeUploadFileNameFeature::class);

        parent::tearDown();
    }

    public function testShouldGenerateUniquieNameUniqueUploadFileName()
    {
        $this->assertNotEquals('foo.jpg', sanitize_file_name('foo.jpg'));
        $this->assertNotEquals('foo.jpeg', sanitize_file_name('foo.jpeg'));
        $this->assertNotEquals('foo.png', sanitize_file_name('foo.png'));
        $this->assertNotEquals('foo.gif', sanitize_file_name('foo.gif'));
        $this->assertNotEquals('foo.bmp', sanitize_file_name('foo.bmp'));
        $this->assertNotEquals('foo.svg', sanitize_file_name('foo.svg'));
        $this->assertNotEquals('foo.webp', sanitize_file_name('foo.webp'));
    }

    public function testShouldNotGenerateUniquieNameUniqueUploadFileName()
    {
        $this->assertEquals('foo', sanitize_file_name('foo'));
        $this->assertEquals('foo.doc', sanitize_file_name('foo.doc'));
    }
}
