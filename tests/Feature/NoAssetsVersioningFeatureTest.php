<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoAssetsVersioningFeature;

class NoAssetsVersioningFeatureTest extends \WP_UnitTestCase
{
    private $old_wp_scripts = null;

    private $old_wp_styles = null;

    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoAssetsVersioningFeature::class);

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
        FeatureFactory::unload(NoAssetsVersioningFeature::class);

        $GLOBALS['wp_scripts'] = $this->old_wp_scripts;

        add_action('wp_default_scripts', 'wp_default_scripts');

        $GLOBALS['wp_styles'] = $this->old_wp_styles;

        add_action('wp_default_styles', 'wp_default_styles');
        add_action('wp_print_styles', 'print_emoji_styles');

        parent::tearDown();
    }

    public function testScriptsDisableAssetsVersioning()
    {
        wp_enqueue_script('no-deps-no-version', 'example.com', []);
        wp_enqueue_script('empty-deps-no-version', 'example.com');
        wp_enqueue_script('empty-deps-version', 'example.com', [], 1.2);
        wp_enqueue_script('empty-deps-null-version', 'example.com', [], null);

        $expected = "<script type='text/javascript' src='http://example.com'></script>\n";
        $expected .= "<script type='text/javascript' src='http://example.com'></script>\n";
        $expected .= "<script type='text/javascript' src='http://example.com'></script>\n";
        $expected .= "<script type='text/javascript' src='http://example.com'></script>\n";

        $this->assertEquals($expected, get_echo('wp_print_scripts'));
    }

    public function testStylesDisableAssetsVersioning()
    {
        wp_enqueue_style('no-deps-no-version', 'example.com');
        wp_enqueue_style('no-deps-version', 'example.com', [], 1.2);
        wp_enqueue_style('no-deps-null-version', 'example.com', [], null);
        wp_enqueue_style('no-deps-null-version-print-media', 'example.com', [], null, 'print');

        $expected = "<link rel='stylesheet' id='no-deps-no-version-css'  href='http://example.com' type='text/css' media='all' />\n";
        $expected .= "<link rel='stylesheet' id='no-deps-version-css'  href='http://example.com' type='text/css' media='all' />\n";
        $expected .= "<link rel='stylesheet' id='no-deps-null-version-css'  href='http://example.com' type='text/css' media='all' />\n";
        $expected .= "<link rel='stylesheet' id='no-deps-null-version-print-media-css'  href='http://example.com' type='text/css' media='print' />\n";

        $this->assertEquals($expected, get_echo('wp_print_styles'));
    }
}
