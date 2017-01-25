<?php
namespace Itgalaxy\Pillar\Util;

class HTML
{
    /**
     * Remove unnecessary self-closing tags
     *
     * @param  string $input Input contain self closing tags.
     *
     * @return string Cleaned input withput self closing tags.
     */
    public static function removeSelfClosingTags($input)
    {
        return str_replace([' />', '/>'], '>', $input);
    }
}
