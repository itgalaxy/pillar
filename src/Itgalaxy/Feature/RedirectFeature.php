<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class RedirectFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('query_vars', [$this, 'queryVars']);
        add_action('wp', [$this, 'redirect']);
    }

    public function redirect()
    {
        $location = get_query_var('redirect', null);

        if ($location) {
            $sanitizedLocation = wp_sanitize_redirect($location);
            $location = wp_validate_redirect($sanitizedLocation);

            $code = get_query_var('redirect-code', 301);

            if ($location) {
                wp_safe_redirect($location, $code);
            } else {
                // @codingStandardsIgnoreStart
                wp_redirect($sanitizedLocation, $code);
                // @codingStandardsIgnoreEnd
            }

            exit();
        }
    }

    public function queryVars($queryVars)
    {
        $queryVars[] = 'redirect';
        $queryVars[] = 'redirect-code';

        return $queryVars;
    }
}
