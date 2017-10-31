<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class FaviconIcoFeature extends FeatureAbstract
{
    protected $options = [
        'icons' => [
            ['width' => 16, 'height' => 16],
            ['width' => 24, 'height' => 24],
            ['width' => 32, 'height' => 32],
            ['width' => 48, 'height' => 48],
            ['width' => 64, 'height' => 64]
        ]
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('add_attachment', [$this, 'generateIcoIcon']);
        add_filter('edit_attachment', [$this, 'generateIcoIcon']);
        add_action('delete_attachment', [$this, 'deleteIcoIcon']);
    }

    public function generateIcoIcon($postID)
    {
        $postMeta = get_post_meta($postID);

        if (empty($postMeta) || empty($postMeta['_wp_attachment_context'])) {
            return;
        }

        $context = $postMeta['_wp_attachment_context'];

        if (!in_array('site-icon', $context)) {
            return;
        }

        if (!is_writable(ABSPATH)) {
            return;
        }

        $icoSizes = [];

        foreach ($this->options['icons'] as $size) {
            if (empty($size['width'])
                || !is_int($size['width'])
                || empty($size['height'])
                || !is_int($size['height'])
            ) {
                continue;
            }

            $icoSizes[] = [$size['width'], $size['height']];
        }

        $source = get_attached_file($postID);
        $destination = ABSPATH . 'favicon.ico';

        $phpIcon = new \PHP_ICO($source, $icoSizes);
        $phpIcon->save_ico($destination);
    }

    public function deleteIcoIcon($post_id)
    {
        $site_icon_id = get_option('site_icon');
        $faviconIcoPath = ABSPATH . 'favicon.ico';

        if ($site_icon_id && $post_id == $site_icon_id && file_exists($faviconIcoPath)) {
            unlink($faviconIcoPath);
        }
    }
}
