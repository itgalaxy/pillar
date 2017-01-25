<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoXmlrpcFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('xmlrpc_enabled', [$this, '__return_false']);
    }
}
