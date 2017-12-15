<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class DangerousFilesCleanerFeature extends FeatureAbstract
{
    private $coreDangerousFiles = [
        'readme.html',
        'wp-config-sample.php'
    ];

    private $dangerousFiles = [
        'readme',
        'readme.txt',
        'readme.html',
        'readme.md',
        'changelog',
        'changelog.txt',
        'changelog.html',
        'changelog.md',
        'contributing',
        'contributing.txt',
        'contributing.html',
        'contributing.md',
        'composer.json',
        'composer.lock'
    ];

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
        $this->removeCoreDangerousFiles();

        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        foreach ($plugins as $pluginName => $plugin) {
            $result = [];
            $destination = trailingslashit(dirname(WP_PLUGIN_DIR . '/' . $pluginName));

            $result['destination'] = $destination;
            $result['source_files'] = array_map(
                function ($filePath) use ($destination) {
                    return str_replace($destination, '', $filePath);
                },
                glob($destination . '*')
            );

            $this->removePluginOrThemeDangerousFiles($result);
        }

        $themes = wp_get_themes([
            'errors' => null
        ]);

        foreach ($themes as $themeName => $theme) {
            $result = [];
            $destination = trailingslashit(trailingslashit($theme->theme_root) . $themeName);

            $result['destination'] = $destination;
            $result['source_files'] = array_map(
                function ($filePath) use ($destination) {
                    return str_replace($destination, '', $filePath);
                },
                glob($destination . '*')
            );

            $this->removePluginOrThemeDangerousFiles($result);
        }
    }

    public function removeDangerousAfterUpgrade($upgrader, $meta)
    {
        if ($meta['action'] != 'install' && $meta['action'] != 'update') {
            return;
        }

        if ($meta['type'] == 'core') {
            $this->removeCoreDangerousFiles();
        }

        if ($meta['type'] == 'plugin') {
            $this->removePluginOrThemeDangerousFiles($upgrader->result);
        }

        if ($meta['type'] == 'theme') {
            $this->removePluginOrThemeDangerousFiles($upgrader->result);
        }
    }

    private function removeCoreDangerousFiles()
    {
        if (!function_exists('get_home_path')) {
            include_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $homePath = trailingslashit(get_home_path());

        foreach($this->coreDangerousFiles as $coreDangerousFile) {
            $filePath = $homePath . $coreDangerousFile;

            if (is_file($filePath) && file_exists($filePath)) {
                try {
                    unlink($filePath);
                } catch (\Exception $exception) {
                    // Nothing
                }
            }
        }
    }

    private function removePluginOrThemeDangerousFiles($result)
    {
        if (!isset($result['destination']) && !isset($result['source_files'])) {
            return;
        }

        $destination = $result['destination'];
        $sourceFiles = $result['source_files'];

        $strtolower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

        foreach ($sourceFiles as $sourceFile) {
            if (!in_array($strtolower($sourceFile), $this->dangerousFiles)) {
                continue;
            }

            $filePath = $destination . $sourceFile;

            if (is_file($filePath) && file_exists($filePath)) {
                try {
                    unlink($filePath);
                } catch (\Exception $exception) {
                    // Nothing
                }
            }
        }
    }
}
