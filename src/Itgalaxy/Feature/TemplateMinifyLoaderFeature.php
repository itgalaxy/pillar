<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class TemplateMinifyLoaderFeature extends FeatureAbstract
{
    protected $options = [
        'allowedExtensions' => ['php'],
        'customFilters' => [],
        'directory' => ''
    ];

    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        $options = $this->options;

        $templateFilters = ['template_include'];

        if (is_plugin_active('woocommerce/woocommerce.php')) {
            $templateFilters[] = 'woocommerce_locate_template';
            $templateFilters[] = 'wc_get_template_part';
        }

        if (count($options['customFilters']) > 0) {
            $templateFilters = array_merge($templateFilters, $options['customFilters']);
        }

        foreach ($templateFilters as $templateFilter) {
            add_filter($templateFilter, [$this, 'minifyTemplate'], 100);
        }
    }

    public function minifyTemplate($template)
    {
        // Avoid problem when plugin pass array of templates
        if (!is_string($template) || empty($template)) {
            return $template;
        }

        $options = $this->options;
        $pathInfo = pathinfo($template);

        if (!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $options['allowedExtensions'])) {
            return $template;
        }

        $nameResolver = isset($options['nameResolver'])
            ? $options['nameResolver']
            : function ($template, $pathInfo) {
                return hash('md4', $template)
                    . '.'
                    . $pathInfo['extension'];
            };

        $minifiedTemplate = trailingslashit(realpath($options['directory'])) . $nameResolver($template, $pathInfo);

        if (file_exists($minifiedTemplate)) {
            return $minifiedTemplate;
        }

        return $template;
    }
}
