<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class SupportWebpFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('upload_mimes', [$this, 'supportWebp']);
    }

    public function supportWebp($extensions)
    {
        $extensions['webp'] = 'image/webp';

        return $extensions;
    }
}
