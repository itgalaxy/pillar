<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class HeadersCleanUpFeature extends FeatureAbstract
{
    protected $options = [
        'rest' => true,
        'pingback' => true
    ];
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $options = $this->options;

        if ($options['rest'] && has_action('template_redirect', 'rest_output_link_header') !== false) {
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }

        if ($options['pingback']) {
            add_filter(
                'wp_headers',
                function (array $headers) {
                    if (isset($headers['X-Pingback'])) {
                        unset($headers['X-Pingback']);
                    }

                    return $headers;
                }
            );
        }
    }
}
