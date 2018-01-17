<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class EmbedMarkupWrapperFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('embed_defaults', [$this, 'unsetDefaultWidthAndHeight']);
        add_filter('embed_oembed_html', [$this, 'wrapEmbed'], 10, 3);
    }

    public function unsetDefaultWidthAndHeight($attr)
    {
        if (isset($attr['width'])) {
            $attr['width'] = null;
        }

        if (isset($attr['height'])) {
            $attr['height'] = null;
        }

        return $attr;
    }

    /**
     * Wrap embedded media as suggested by Readability.
     *
     * @link https://gist.github.com/965956
     * @link http://www.readability.com/publishers/guidelines#publisher
     *
     * @param string $html Embed html.
     *
     * @return string Wrapped embed.
     */
    public function wrapEmbed($html, $url, $attr)
    {
        // Remove deprecated `frameborder` attribute.
        $html = preg_replace('/frameborder="\d*"\s/', '', $html);

        // Strip width and height from embeded object
        $html = preg_replace('/(width|height)="\d*"\s/', '', $html);

        $output = '';
        $styleAttr = [];

        if (isset($attr['width'])) {
            $styleAttr['width'] = is_numeric($attr['width']) ? $attr['width'] . 'px' : $attr['width'];
        }

        if (isset($attr['height'])) {
            $styleAttr['height'] = is_numeric($attr['height']) ? $attr['height'] . 'px' : $attr['height'];
        }

        $styleAttrHTML = '';

        foreach ($styleAttr as $property => $value) {
            $styleAttrHTML = $property . ':' . $value . ';';
        }

        $aspectRatio = !empty($attr['aspect-ratio']) ? $attr['aspect-ratio'] : '16by9';

        $output .= '<div class="embed"' . (!empty($styleAttrHTML) ? ' style="' . $styleAttrHTML . '"' : '') . '>';
        $output .= '<div class="embed-responsive embed-responsive-' . $aspectRatio . '">'
            . $html
            . '</div>';
        $output .= '</div>';

        return $output;
    }
}
