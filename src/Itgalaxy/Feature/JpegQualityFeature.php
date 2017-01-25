<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class JpegQualityFeature extends FeatureAbstract
{
    protected $options = [
        'quality' => 100
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('jpeg_quality', [$this, 'getQuality']);
    }

    public function getQuality()
    {
        return (int) $this->options['quality'];
    }
}
