<?php
namespace Itgalaxy\Pillar\Util;

class Str
{
    /**
     * Remove unnecessary self-closing tags
     *
     * @param  string $input Input contain newlines.
     *
     * @return string Cleaned input without newlines.
     */
    public static function removeNewLine($input)
    {
        return str_replace(["\n", "\r"], '', $input);
    }

    public static function snake($input, $delimiter = '_')
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode($delimiter, $ret);
    }
}
