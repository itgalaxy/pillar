<?php
namespace Itgalaxy\Pillar\Base;

use Itgalaxy\Pillar\Util\Str;

class FeatureFactory
{
    public static $optionName = 'pillar-plugin';

    protected static $features = [];

    protected static $featuresDirectory = 'src/Itgalaxy/Feature';

    public static function loadFeatures()
    {
        global $_wp_theme_features;

        $absoluteFeaturesDirectory = realpath(__DIR__ . '/../../../' . self::$featuresDirectory);

        $options = get_option(self::$optionName, []);

        $action = null;

        if (!empty($options) && isset($options['action']) && $options['action'] === 'activation') {
            $action = 'activation';
        }

        if (empty($_wp_theme_features)) {
            return;
        }

        $substr = function_exists('mb_substr') ? 'mb_substr' : 'substr';
        $enabledFeatures = array_filter(
            $_wp_theme_features,
            function ($key) use ($substr) {
                if ($substr($key, 0, 6) === 'pillar') {
                    return true;
                }

                return false;
            },
            ARRAY_FILTER_USE_KEY
        );

        $existsFeatures = [];

        foreach (glob($absoluteFeaturesDirectory . '/*.php') as $filePath) {
            $filePath = realpath($filePath);
            $className = basename($filePath, '.php');
            $featureName = 'pillar-' . str_replace('-feature', '', Str::snake($className, '-'));
            $existsFeatures[$featureName] = [
                'class' => $className
            ];
        }

        foreach ($enabledFeatures as $featureName => $featureOptions) {
            if (isset($existsFeatures[$featureName])) {
                $featureOptions = is_array($featureOptions)
                    ? $featureOptions[0]
                    : [];
                $className = $existsFeatures[$featureName]['class'];

                self::loadFeature('\\Itgalaxy\\Pillar\\Feature\\' . $className, $featureOptions, $action);
            } else {
                throw new \Exception('Not found ' . $featureName . ' feature');
            }
        }

        if (!empty($action) && $action == 'activation') {
            update_option(self::$optionName, ['action' => null]);
        }
    }

    public static function loadFeature($featureClass, $options = [], $action = null)
    {
        if (!isset(self::$features[$featureClass])) {
            self::$features[$featureClass] = new $featureClass(is_array($options) ? $options : []);
            self::$features[$featureClass]->initialize();

            if (!empty($action)
                && $action === 'activation'
                && method_exists(self::$features[$featureClass], 'activation')
            ) {
                self::$features[$featureClass]->activation();
            }
        }

        return self::$features[$featureClass];
    }

    public static function unload($featureClass)
    {
        if (!isset(self::$features[$featureClass])) {
            throw new \Exception('Feature ' . $featureClass . ' is not setup');
        }

        unset(self::$features[$featureClass]);
    }

    public static function getFeatures()
    {
        return self::$features;
    }
}
