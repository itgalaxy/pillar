<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\ConvertBmpToJpgFeature;

class ConvertBmpToJpgFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(ConvertBmpToJpgFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(ConvertBmpToJpgFeature::class);
        $this->remove_added_uploads();

        parent::tearDown();
    }

    public function testSupportBmpMimeTypeAllowed()
    {
        $this->assertArraySubset(
            [
                'bmp' => 'image/bmp'
            ],
            get_allowed_mime_types()
        );
    }

    public function testMimeToExt()
    {
        $mimeToExt = apply_filters('getimagesize_mimes_to_exts', []);

        $this->assertArraySubset([
            'image/bmp' => 'bmp',
            'image/x-ms-bmp' => 'bmp',
            'image/x-windows-bmp' => 'bmp',
            'image/x-bmp' => 'bmp'
        ], $mimeToExt);
    }

    public function testInsertBmpImage()
    {
        if (!function_exists('imagejpeg')) {
            $this->markTestSkipped('jpeg support unavailable');
        }

        $filename = PLUGIN_DIR_TESTDATA . '/lenna-gray.bmp';
        $contents = file_get_contents($filename);

        $upload = wp_upload_bits(basename($filename), null, $contents);
        $this->assertTrue(empty($upload['error']));

        $id = $this->_make_attachment($upload);
        $uploads = wp_upload_dir();

        $thumb = image_get_intermediate_size($id, 'thumbnail');
        $this->assertEquals('lenna-gray-150x150.jpg', $thumb['file']);
        $this->assertTrue(is_file($uploads['basedir'] . DIRECTORY_SEPARATOR . $thumb['path']));
    }

    public function testInsertCorruptBmpImage()
    {
        if (!function_exists('imagejpeg')) {
            $this->markTestSkipped('jpeg support unavailable');
        }

        $filename = PLUGIN_DIR_TESTDATA . '/lenna-corrupt.bmp';
        $contents = file_get_contents($filename);

        $upload = wp_upload_bits(basename($filename), null, $contents);
        $error = $upload['error'];

        $this->assertTrue($error['message'] === 'Error creating image from bmp format');
    }

    public function testInsertPngImage()
    {
        if (!function_exists('imagejpeg')) {
            $this->markTestSkipped('jpeg support unavailable');
        }

        $filename = PLUGIN_DIR_TESTDATA . '/lenna.png';
        $contents = file_get_contents($filename);

        $upload = wp_upload_bits(basename($filename), null, $contents);
        $this->assertTrue(empty($upload['error']));
    }
}
