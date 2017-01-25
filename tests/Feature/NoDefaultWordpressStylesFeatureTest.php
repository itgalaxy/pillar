<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Base\FeatureFactory;
use Itgalaxy\Pillar\Feature\NoDefaultWordpressStylesFeature;

class NoDefaultWordpressStylesFeatureTest extends \WP_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        FeatureFactory::loadFeature(NoDefaultWordpressStylesFeature::class);
    }

    public function tearDown()
    {
        FeatureFactory::unload(NoDefaultWordpressStylesFeature::class);

        parent::tearDown();
    }

    public function clean_up_global_scope()
    {
        global $wp_widget_factory,
            $wp_registered_sidebars,
            $wp_registered_widgets,
            $wp_registered_widget_controls,
            $wp_registered_widget_updates;

        $wp_registered_sidebars = [];
        $wp_registered_widgets = [];
        $wp_registered_widget_controls = [];
        $wp_registered_widget_updates = [];
        $wp_widget_factory->widgets = [];

        parent::clean_up_global_scope();
    }

    public function testDisableDefaultGalleryStyle()
    {
        $style = apply_filters('use_default_gallery_style', 1);

        $this->assertTrue($style === null);
    }

    public function testDisableDefaultWidgetRecentCommentsStyle()
    {
        global $wp_registered_widgets;

        $this->assertEmpty($wp_registered_widgets);
        wp_widgets_init();

        global $wp_widget_factory;

        if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
            $this->assertFalse(
                has_action(
                    'wp_head',
                    [
                        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
                        'recent_comments_style'
                    ]
                )
            );
        }
    }
}
