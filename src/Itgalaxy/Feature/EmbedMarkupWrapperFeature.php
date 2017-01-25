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
        add_filter('embed_oembed_html', [$this, 'embedOembedHtml']);
    }

    /**
     * Wrap embedded media as suggested by Readability.
     *
     * @link https://gist.github.com/965956
     * @link http://www.readability.com/publishers/guidelines#publisher
     *
     * @param string $cache Embed.
     *
     * @return string Wrapped embed.
     */
    public function embedOembedHtml($cache)
    {
        return '<div class="embed">' . $cache . '</div>';
    }
}
