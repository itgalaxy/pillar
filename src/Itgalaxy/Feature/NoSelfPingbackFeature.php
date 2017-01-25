<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoSelfPingbackFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('pre_ping', function (&$links) {
            $home = get_option('home');

            $mb_strpos = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';

            foreach ($links as $l => $link) {
                if ($mb_strpos($link, $home) === 0) {
                    unset($links[$l]);
                }
            }
        });
    }
}
