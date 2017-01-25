<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoAssetsVersioningFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('script_loader_src', [$this, 'removeAssetsVersion']);
        add_filter('style_loader_src', [$this, 'removeAssetsVersion']);
    }

    public function removeAssetsVersion($src)
    {
        return $src ? esc_url(remove_query_arg('ver', $src)) : false;
    }
}
