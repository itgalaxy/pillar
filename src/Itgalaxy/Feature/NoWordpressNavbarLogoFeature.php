<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoWordpressNavbarLogoFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('wp_before_admin_bar_render', [$this, 'removeLogoFromAdminBar']);
    }

    /**
     * Remove the WordPress logo from admin bar.
     *
     * @return void
     */
    public function removeLogoFromAdminBar()
    {
        global $wp_admin_bar;

        $wp_admin_bar->remove_menu('wp-logo');
    }
}
