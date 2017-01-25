<?php
namespace Itgalaxy\Pillar\Util;

class URL
{
    public static function toRelative($input)
    {
        if (is_feed()) {
            return $input;
        }

        $url = wp_parse_url($input);

        if (!isset($url['host']) || !isset($url['path'])) {
            return $input;
        }

        // Fallback to home_url
        $siteUrl = wp_parse_url(network_home_url());

        if (!isset($url['scheme'])) {
            $url['scheme'] = $siteUrl['scheme'];
        }

        $hostsMatch = $siteUrl['host'] === $url['host'];
        $schemesMatch = $siteUrl['scheme'] === $url['scheme'];
        $portsExist = isset($siteUrl['port']) && isset($url['port']);
        $portsMatch = $portsExist ? $siteUrl['port'] === $url['port'] : true;

        if ($hostsMatch && $schemesMatch && $portsMatch) {
            return wp_make_link_relative($input);
        }

        return $input;
    }
}
