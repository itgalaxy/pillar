<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ExcerptMoreFeature extends FeatureAbstract
{
    protected $options = [
        'more' => '...'
    ];

    public function initialize()
    {
        add_filter('excerpt_more', function () {
            return $this->options['more'];
        });
    }
}
