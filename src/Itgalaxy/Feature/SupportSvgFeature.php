<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class SupportSvgFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('upload_mimes', [$this, 'supportSvg']);
        add_filter('wp_get_attachment_image_src', [$this, 'removeWidthAndHeight'], 10, 2);
        add_filter('wp_prepare_attachment_for_js', [$this, 'prepareAttachmentForJs'], 10, 2);
        add_action('admin_init', [$this, 'addCapability']);

        // Fix for 4.7.*
        add_filter('wp_check_filetype_and_ext', [$this, 'svgDisableRealMimeCheck'], 10, 4);
    }

    public function supportSvg($mimes)
    {
        // Svg can contain `script` tag and contain xss
        if (!current_user_can('upload_svg_files')) {
            return $mimes;
        }

        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';

        return $mimes;
    }

    public function removeWidthAndHeight($image, $attachmenId)
    {
        if (isset($image[0])) {
            $path = get_attached_file($attachmenId);
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if ($extension && $extension === 'svg') {
                $size = $this->getSvgImageSize($path);

                if (isset($size['width']) && isset($size['height'])) {
                    $image[1] = $size['width'];
                    $image[2] = $size['height'];
                } else {
                    $image[1] = null;
                    $image[2] = null;
                }

                return $image;
            }
        }

        return $image;
    }

    public function prepareAttachmentForJs($response, $attachment)
    {
        if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
            $path = get_attached_file($attachment->ID);

            $fullSize = [
                'url' => $response['url']
            ];

            $size = $this->getSvgImageSize($path);

            if (isset($size['width']) && isset($size['height'])) {
                $fullSize['width'] = $size['width'];
                $fullSize['height'] = $size['height'];
                $fullSize['orientation'] = $size['height'] > $size['width']
                    ? 'portrait'
                    : 'landscape';
            }

            $response['sizes']['full'] = $fullSize;
        }

        return $response;
    }

    public function svgDisableRealMimeCheck($data, $file, $filename, $mimes)
    {
        $wp_filetype = wp_check_filetype($filename, $mimes);

        $ext = $wp_filetype['ext'];
        $type = $wp_filetype['type'];
        $proper_filename = $data['proper_filename'];

        return compact('ext', 'type', 'proper_filename');
    }

    public function addCapability()
    {
        $roles = get_editable_roles();
        $grantRoles = ['administrator'];

        foreach ($roles as $roleName => $_) {
            $role = get_role($roleName);
            $role->add_cap('upload_svg_files', in_array($roleName, $grantRoles));
        }
    }

    private function getSvgImageSize($path)
    {
        $size = [
            'width' => null,
            'height' => null
        ];

        if (class_exists('SimpleXMLElement')) {
            if (!file_exists($path)) {
                return $size;
            }

            $contents = file_get_contents($path);

            if ($contents === false) {
                return $size;
            }

            $svg = new \SimpleXMLElement($contents);

            if (isset($svg['width'])) {
                $size['width'] = (int) $svg['width'];
            }

            if (isset($svg['height'])) {
                $size['height'] = (int) $svg['height'];
            }
        }

        return $size;
    }
}
