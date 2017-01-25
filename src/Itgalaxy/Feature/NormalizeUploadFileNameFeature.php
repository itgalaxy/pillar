<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NormalizeUploadFileNameFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('sanitize_file_name', [$this, 'uniqueFilename']);
    }

    public function uniqueFilename($filename)
    {
        $info = pathinfo($filename);
        $ext = empty($info['extension']) ? '' : '.' . $info['extension'];

        $includeExtension = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'bmp',
            'svg',
            'svgz',
            'webp'
        ];

        $strtolowerFn = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

        if (!empty($info['extension']) && in_array($strtolowerFn($info['extension']), $includeExtension)) {
            return md5(uniqid(rand(), true)) . $ext;
        }

        return $filename;
    }
}
