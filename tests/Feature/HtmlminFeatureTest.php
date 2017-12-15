<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\HtmlminFeature;

class HtmlminFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(HtmlminFeature::class);

        $this->old_wp_scripts = isset($GLOBALS['wp_scripts']) ? $GLOBALS['wp_scripts'] : null;

        remove_action('wp_default_scripts', 'wp_default_scripts');

        $GLOBALS['wp_scripts'] = new \WP_Scripts();
        $GLOBALS['wp_scripts']->default_version = get_bloginfo('version');

        if (empty($GLOBALS['wp_styles'])) {
            $GLOBALS['wp_styles'] = null;
        }

        $this->old_wp_styles = $GLOBALS['wp_styles'];

        remove_action('wp_default_styles', 'wp_default_styles');
        remove_action('wp_print_styles', 'print_emoji_styles');

        $GLOBALS['wp_styles'] = new \WP_Styles();
        $GLOBALS['wp_styles']->default_version = get_bloginfo('version');
    }

    public function tearDown()
    {
        FeatureFactory::unloadFeature(HtmlminFeature::class);

        $GLOBALS['wp_scripts'] = $this->old_wp_scripts;

        add_action('wp_default_scripts', 'wp_default_scripts');

        $GLOBALS['wp_styles'] = $this->old_wp_styles;

        add_action('wp_default_styles', 'wp_default_styles');
        add_action('wp_print_styles', 'print_emoji_styles');

        parent::tearDown();
    }

    public function testCleanStyleTag()
    {
        $style = '.foo {}';

        wp_enqueue_style('no-deps-no-version', 'example.com');
        wp_enqueue_style('no-deps-version', 'example.com', [], 1.2);
        wp_enqueue_style('no-deps-null-version', 'example.com', [], null);
        wp_enqueue_style('no-deps-null-version-print-media', 'example.com', [], null, 'print');
        wp_enqueue_style('handle', 'http://example.com', [], 1);
        wp_add_inline_style('handle', $style);

        $version = get_bloginfo('version');
        $expected = "<link rel=\"stylesheet\" href=\"http://example.com?ver={$version}\">"
            . '<link rel="stylesheet" href="http://example.com?ver=1.2">'
            . '<link rel="stylesheet" href="http://example.com">'
            . '<link rel="stylesheet" href="http://example.com" media="print">'
            . '<link rel="stylesheet" href="http://example.com?ver=1">'
            . "<style id='handle-inline-css' type='text/css'>\n.foo {}\n</style>\n";

        $this->assertEquals($expected, get_echo('wp_print_styles'));
    }

    public function testCleanScriptTag()
    {
        wp_enqueue_script('no-deps-no-version', 'example.com', []);
        wp_enqueue_script('empty-deps-no-version', 'example.com');
        wp_enqueue_script('empty-deps-version', 'example.com', [], 1.2);
        wp_enqueue_script('empty-deps-null-version', 'example.com', [], null);


        $version = get_bloginfo('version');
        $expected = "<script src=\"http://example.com?ver={$version}\"></script>"
            . "<script src=\"http://example.com?ver={$version}\"></script>"
            . '<script src="http://example.com?ver=1.2"></script>'
            . '<script src="http://example.com"></script>';

        $this->assertEquals($expected, get_echo('wp_print_scripts'));
    }
}
