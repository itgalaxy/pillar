<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class AjaxPostViewCounterFeature extends FeatureAbstract
{
    protected $options = [
        'actions' => []
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $options = $this->options;

        if (empty($options['actions'])) {
            return;
        }

        foreach ($options['actions'] as $action) {
            add_action($action, function () {
                if (function_exists('process_postviews')) {
                    process_postviews();
                }
            });
        }
    }
}
