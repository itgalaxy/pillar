<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NormalizeFileExtensionFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('sanitize_file_name', [$this, 'sanitizeFileName']);
    }

    public function sanitizeFileName($filename)
    {
        $extensionReplace = [
            'jpeg' => 'jpg',
            'jpe' => 'jpg',
            'jfi' => 'jpg',
            'jfif' => 'jpg',
            'j' => 'jpg',
            'jif' => 'jpg',
            'jmh' => 'jpg'
        ];

        $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
        $info = pathinfo($filename);
        $ext = empty($info['extension']) ? '' : $strtolower($info['extension']);

        if (!empty($ext) && !empty($extensionReplace[$ext])) {
            return $info['filename'] . '.' . $extensionReplace[$ext];
        }

        return $filename;
    }
}
