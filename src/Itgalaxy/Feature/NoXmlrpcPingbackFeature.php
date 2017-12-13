<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoXmlrpcPingbackFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('xmlrpc_methods', [$this, 'filterXMLRPCMethod']);
        add_action('xmlrpc_call', [$this, 'killXmlrpcAction']);
        add_filter('bloginfo_url', [$this, 'killPingbackUrl'], 10, 2);
        add_filter('bloginfo', [$this, 'killPingbackUrl'], 10, 2);
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

    /**
     * Disable pingback XMLRPC method.
     *
     * @param mixed[] $methods All XMPRPC methods.
     *
     * @return mixed[] Returns filtered XMPRPC methods.
     */
    public function filterXMPRPCMethod(array $methods)
    {
        unset($methods['pingback.ping']);
        unset($methods['pingback.extensions.getPingbacks']);

        return $methods;
    }

    /**
     * Disable XMLRPC call.
     *
     * @param string $action Action of XMPRPC.
     *
     * @return void
     */
    public function killXMPRPCAction($action)
    {
        if ($action === 'pingback.ping' || $action === 'pingback.extensions.getPingbacks') {
            wp_die(
                'Pingbacks are not supported',
                'Not Allowed!',
                [
                    'response' => 403
                ]
            );
            // escape ok
        }
    }

    /**
     * Kill bloginfo('pingback_url').
     *
     * @param string  $output Output of variable.
     * @param boolean $show   Show variable.
     *
     * @return string Nothing.
     */
    public function killPingbackUrl($output, $show)
    {
        if ($show === 'pingback_url') {
            $output = '';
        }

        return $output;
    }
}
