<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class ModifyTheReadMore extends FeatureAbstract
{
    public function initialize()
    {
        add_filter('excerpt_more', function () {
            return '...';
        });
    }
}
