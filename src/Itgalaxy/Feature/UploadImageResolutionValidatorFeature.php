<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class UploadImageResolutionValidatorFeature extends FeatureAbstract
{
    protected $options = [
        'maxWidth' => 4096,
        'maxHeight' => 3072
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('wp_handle_upload_prefilter', [$this, 'validateImageSize']);
    }

    public function validateImageSize($file)
    {
        if (empty($file['tmp_name'])) {
            return $file;
        }

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
