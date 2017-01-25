<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Bmp2Image;
use Itgalaxy\Pillar\Base\FeatureAbstract;

class ConvertBmpToJpgFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('upload_mimes', [$this, 'supportBmp']);
        add_filter('getimagesize_mimes_to_exts', [$this, 'mimeToExt']);
        add_filter('wp_handle_upload', [$this, 'bpmToJpeg']);
    }

    public function supportBmp($extensions)
    {
        $extensions['bmp'] = 'image/bmp';

        return $extensions;
    }

    public function mimeToExt($mimeTypes)
    {
        $mimeTypes['image/bmp'] = 'bmp';
        $mimeTypes['image/x-ms-bmp'] = 'bmp';
        $mimeTypes['image/x-windows-bmp'] = 'bmp';
        $mimeTypes['image/x-bmp'] = 'bmp';

        return $mimeTypes;
    }

    public function bpmToJpeg($file)
    {
        $bmpMime = [
            'image/bmp',
            'image/x-ms-bmp',
            'image/x-windows-bmp',
            'image/x-bmp'
        ];

        if (!in_array($file['type'], $bmpMime)) {
            return $file;
        }

        try {
            $bmp = Bmp2Image::make($file['file']);

            $uploads = wp_upload_dir();
            $oldFileName = basename($file['file']);
            $newFileName = basename(str_ireplace('.bmp', '.jpg', $oldFileName));
            $newFileName = wp_unique_filename($uploads['path'], $newFileName);

            if (imagejpeg($bmp, $uploads['path'] . '/' . $newFileName, 100)) {
                imagedestroy($bmp);
                unlink($file['file']);

                $file['file'] = $uploads['path'] . '/' . $newFileName;
                $file['url'] = $uploads['url'] . '/' . $newFileName;
                $file['type'] = 'image/jpeg';

                return $file;
            }

            unlink($file['file']);

            return wp_handle_upload_error(
                $file['file'],
                'Error creating image from bmp format'
            );
        } catch (\Exception $exception) {
            unlink($file['file']);

            return wp_handle_upload_error(
                $file['file'],
                'Error creating image from bmp format'
            );
        }
    }
}
