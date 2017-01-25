<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class NoDangerousFilesFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_action('upgrader_process_complete', [$this, 'removeDangerousAfterUpgrade'], 10, 2);
    }

    public function activation()
    {
        $this->removeDangerousFiles();
    }

    public function removeDangerousAfterUpgrade($upgrader, $meta)
    {
        if ($meta['action'] == 'update' && $meta['type'] == 'core') {
            $this->removeDangerousFiles();
        }
    }

    private function removeDangerousFiles()
    {
        if (!function_exists('get_home_path')) {
            include_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $homePath = get_home_path();

        if (file_exists($homePath . 'readme.html')) {
            unlink($homePath . 'readme.html');
        }

        if (file_exists($homePath . 'license.txt')) {
            unlink($homePath . 'license.txt');
        }

        if (file_exists($homePath . 'wp-config-sample.php')) {
            unlink($homePath . 'wp-config-sample.php');
        }
    }
}
