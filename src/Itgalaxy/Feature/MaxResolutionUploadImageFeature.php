<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class MaxResolutionUploadImageFeature extends FeatureAbstract
{
    protected $options = [
        'maxWidth' => 0,
        'maxHeight' => 0
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        // Check options is set
        if ($this->options['maxWidth'] > 0 && $this->options['maxHeight'] > 0) {
            add_filter('wp_handle_upload_prefilter', [$this, 'validateImageSize']);
        }
    }

    public function validateImageSize($file)
    {
        $image = getimagesize($file['tmp_name']);

        // Width
        if ($image[0] > $this->options['maxWidth']) {
            $file['error'] = 'Превышена максимальная ширина изображения - '
                . (int) $this->options['maxWidth']
                . 'px. '
                . 'Ширина текущего изображения - '
                . $image[0]
                . 'px.';

            return $file;
        }

        // Height
        if ($image[1] > $this->options['maxHeight']) {
            $file['error'] = 'Превышена максимальная высота изображения - '
                . (int) $this->options['maxWidth']
                . 'px. '
                . 'Высота текущего изображения - '
                . $image[1]
                . 'px.';

            return $file;
        }

        return $file;
    }
}
