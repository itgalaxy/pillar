<?php
namespace Itgalaxy\Pillar\Base;

class FeatureFactory
{
    private $optionName = 'pillar-plugin';

    private $features = [];

    private $namespace = '\\Itgalaxy\\Pillar\\Feature\\';

    public function loadFeatures()
    {
        global $_wp_theme_features;

        $options = get_option($this->optionName, []);

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

        foreach ($enabledFeatures as $featureName => $featureOptions) {
            $className = str_replace(
                '-',
                '',
                ucwords(str_replace('pillar-', '', $featureName . '-feature'), '-')
            );

            $options = is_array($featureOptions) && isset($featureOptions[0])
                ? $featureOptions[0]
                : [];

            try {
                $this->loadFeature($className, $options, $action);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }

        if (!empty($action) && $action == 'activation') {
            update_option($this->optionName, ['action' => null]);
        }
    }

    public function loadFeature($featureClass, $options = [], $action = null)
    {
        $fullyQualifiedClassName = $this->namespace . $featureClass;

        if (!isset($this->features[$fullyQualifiedClassName])) {
            $this->features[$fullyQualifiedClassName] = new $fullyQualifiedClassName(
                is_array($options) ? $options : []
            );
            $this->features[$fullyQualifiedClassName]->initialize();

            if (!empty($action)
                && $action === 'activation'
                && method_exists($this->features[$fullyQualifiedClassName], 'activation')
            ) {
                $this->features[$fullyQualifiedClassName]->activation();
            }
        }

        return $this->features[$fullyQualifiedClassName];
    }

    public function unloadFeature($featureClass)
    {
        $fullyQualifiedClassName = $this->namespace . $featureClass;

        if (!isset($this->features[$fullyQualifiedClassName])) {
            throw new \Exception('Feature ' . $fullyQualifiedClassName . ' is not setup');
        }

        unset($this->features[$featureClass]);
    }

    public function getFeatures()
    {
        return $this->features;
    }

    public function getOptionName()
    {
        return $this->optionName;
    }
}
