<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoUnnecessaryDashboardWidgetsFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('admin_init', [$this, 'removeDashboardWidgets']);
    }

    /**
     * Remove unnecessary dashboard widgets.
     *
     * @link http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
     *
     * @return void
     */
    public function removeDashboardWidgets()
    {
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('dashboard_primary', 'dashboard', 'normal');
        remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
    }
}
