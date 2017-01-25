<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\EmbedMarkupWrapperFeature;

class MarkupWrapperFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(EmbedMarkupWrapperFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(EmbedMarkupWrapperFeature::class);

        parent::tearDown();
    }

    public function testEmbeddedWrap()
    {
        $user = self::factory()->user->create_and_get([
            'display_name' => 'John Doe',
        ]);
        $post_id = self::factory()->post->create([
            'post_author' => $user->ID,
            'post_title' => 'Hello World',
            'post_content' => 'Foo Bar',
            'post_excerpt' => 'Bar Baz'
        ]);
        $file = PLUGIN_DIR_TESTDATA . '/images/canola.jpg';
        $attachment_id = self::factory()->attachment->create_object($file, $post_id, [
            'post_mime_type' => 'image/jpeg',
        ]);
        set_post_thumbnail($post_id, $attachment_id);
        $this->go_to(get_post_embed_url($post_id));

        $this->assertQueryTrue('is_single', 'is_singular', 'is_embed');

        ob_start();

        include ABSPATH . WPINC . '/theme-compat/embed.php';

        $actual = ob_get_clean();

        $doc = new \DOMDocument();
        $this->assertTrue($doc->loadHTML($actual));
        $this->assertFalse(strpos($actual, 'That embed can&#8217;t be found.'));
        $this->assertNotFalse(strpos($actual, 'Hello World'));
    }
}
