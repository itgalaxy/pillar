<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoRestApiFeature extends FeatureAbstract
{
    protected $options = [
        'allowedRoutes' => []
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        if (version_compare(get_bloginfo('version'), '4.7', '>=')) {
            add_filter('rest_authentication_errors', function () {
                global $wp;

                if (!empty($wp->query_vars['rest_route'])) {
                    $options = $this->options;
                    $route = $wp->query_vars['rest_route'];

                    foreach ($options['allowedRoutes'] as $allowedRoute) {
                        if (preg_match($allowedRoute, $route) === 1) {
                            return true;
                        }
                    }
                }

                return new \WP_Error(
                    'rest_cannot_access',
                    'Only authenticated users can access the REST API',
                    [
                        'status' => rest_authorization_required_code()
                    ]
                );
            }, 0);
        } else {
            // Filters for WP-API version 2.x
            if (has_filter('rest_enabled', '__return_false') === false) {
                add_filter('rest_enabled', '__return_false', 0);
            }
        }

        // Filters for WP-API version 1.x
        if (has_filter('json_enabled', '__return_false') === false) {
            add_filter('json_enabled', '__return_false', 0);
        }

        if (has_filter('json_jsonp_enabled', '__return_false') === false) {
            add_filter('json_jsonp_enabled', '__return_false', 0);
        }

        // Filters for WP-API version 2.x
        if (has_filter('rest_jsonp_enabled', '__return_false') === false) {
            add_filter('rest_jsonp_enabled', '__return_false', 0);
        }

        // Remove REST API info from head and headers
        if (has_action('xmlrpc_rsd_apis', 'rest_output_rsd') !== false) {
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
        }

        if (has_action('wp_head', 'rest_output_link_wp_head') !== false) {
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
        }

        if (has_action('template_redirect', 'rest_output_link_header') !== false) {
            remove_action('template_redirect', 'rest_output_link_header', 11);
        }
    }
}
