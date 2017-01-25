<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class AttachmentMimeTypeClassesFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('wp_get_attachment_image_attributes', [$this, 'addClasses'], 10, 2);
    }

    public function addClasses($attr, $attachment)
    {
        list($type, $subtype) = explode('/', $attachment->post_mime_type);

        $attr['class'] .= ' attachment-type-'
            . $type
            . ' attachment-subtype-'
            . str_replace('+', '-', $subtype);

        return $attr;
    }
}
