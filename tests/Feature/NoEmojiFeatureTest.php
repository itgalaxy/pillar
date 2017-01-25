<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoEmojiFeature;

class NoEmojiFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoEmojiFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(NoEmojiFeature::class);

        parent::tearDown();
    }

    public function testDisableEmoji()
    {
        do_action('init');

        $this->assertFalse(has_action('wp_head', 'print_emoji_detection_script'));
        $this->assertFalse(has_action('admin_print_scripts', 'print_emoji_detection_script'));
        $this->assertFalse(has_action('wp_print_styles', 'print_emoji_styles'));
        $this->assertFalse(has_action('admin_print_styles', 'print_emoji_styles'));
        $this->assertFalse(has_action('the_content_feed', 'wp_staticize_emoji'));
        $this->assertFalse(has_action('comment_text_rss', 'wp_staticize_emoji'));
        $this->assertFalse(has_action('wp_mail', 'wp_staticize_emoji_for_email'));
    }

    public function testDisableEmojiInTinyMcePlugins()
    {
        do_action('init');

        $tinyMcePlugins = apply_filters('tiny_mce_plugins', ['foo', 'wpemoji']);

        $this->assertContains('foo', $tinyMcePlugins);
        $this->assertNotContains('wpemoji', $tinyMcePlugins);
    }

    public function testDisableEmojiInResourceHints()
    {
        do_action('init');
        $resourceHints = get_echo('wp_resource_hints');

        $this->assertTrue(strpos($resourceHints, 'href=\'//s.w.org\'') === false);
    }
}
