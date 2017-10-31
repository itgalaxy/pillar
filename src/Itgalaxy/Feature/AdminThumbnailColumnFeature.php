<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class AdminThumbnailColumnFeature extends FeatureAbstract
{
    protected $options = [
        'width' => 100,
        'height' => 100
    ];

    public function initialize()
    {
        add_filter('manage_posts_columns', [$this, 'addThumbnailColumn'], 10, 2);
        add_action('manage_posts_custom_column', [$this, 'addThumbnailValue'], 10, 2);
        add_filter('manage_pages_columns', [$this, 'addThumbnailColumn']);
        add_action('manage_pages_custom_column', [$this, 'addThumbnailValue'], 10, 2);
    }

    public function addThumbnailColumn($postsColumns, $postType = null)
    {
        if (($postType && !post_type_supports($postType, 'thumbnail'))
            || ($postType && $postType == 'product' && is_plugin_active('woocommerce/woocommerce.php'))
        ) {
            return $postsColumns;
        }

        $postsColumns['post-thumbnail'] = !empty(get_post_type_object($postType)->labels->featured_image)
            ? get_post_type_object($postType)->labels->featured_image
            : __('Featured Images');

        return $postsColumns;
    }

    public function addThumbnailValue($columnName, $postId)
    {
        switch ($columnName) {
            case 'post-thumbnail':
                if (has_post_thumbnail($postId)) {
                    echo wp_kses_post(wp_get_attachment_image(
                        get_post_thumbnail_id($postId),
                        [
                            $this->options['width'],
                            $this->options['height']
                        ],
                        true,
                        [
                            'width' => $this->options['width'],
                            'height' => $this->options['height']
                        ]
                    ));
                } else {
                    echo '—';
                }
                break;
            default:
                // Nothing
                break;
        }
    }
}
