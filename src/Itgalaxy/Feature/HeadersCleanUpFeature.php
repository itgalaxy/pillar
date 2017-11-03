<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class HeadersCleanUpFeature extends FeatureAbstract
{
    protected $options = [
        'rest' => true
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $options = $this->options;

        if ($options['rest']) {
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }
    }
}
