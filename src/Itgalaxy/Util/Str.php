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
}
