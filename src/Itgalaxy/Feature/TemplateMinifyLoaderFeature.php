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

        // Todo Need handle `get_template_part`?
        $templateFilters = [
            'index_template_hierarchy',
            '404_template_hierarchy',
            'archive_template_hierarchy',
            'author_template_hierarchy',
            'category_template_hierarchy',
            'tag_template_hierarchy',
            'taxonomy_template_hierarchy',
            'date_template_hierarchy',
            'home_template_hierarchy',
            'frontpage_template_hierarchy',
            'page_template_hierarchy',
            'paged_template_hierarchy',
            'search_template_hierarchy',
            'single_template_hierarchy',
            'singular_template_hierarchy',
            'attachment_template_hierarchy',
            'comments_popup_template_hierarchy',
            'embed_template_hierarchy'
        ];

        if (is_plugin_active('woocommerce/woocommerce.php')) {
            array_push($templateFilters, 'woocommerce_locate_template');
        }

        if (count($options['customFilters']) > 0) {
            $templateFilters = array_merge($templateFilters, $options['customFilters']);
        }

        foreach ($templateFilters as $templateFilter) {
            add_filter($templateFilter, [$this, 'minifyTemplate'], 100);
        }
    }

    public function minifyTemplate($templates)
    {
        // Avoid strange behaviour for some plugins
        if (!is_array($templates) || empty($templates)) {
            return $templates;
        }

        return array_map(
            function ($template) {
                return $this->getMinifiedTemplate($template);
            },
            $templates
        );
    }

    protected function getMinifiedTemplate($template)
    {
        $options = $this->options;
        $pathInfo = pathinfo($template);

        if (!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $options['allowedExtensions'])) {
            return $template;
        }

        // Better use template for this purpose, example `{filename}.min.{ext}`
        $minifiedTemplate = trailingslashit($options['directory'])
            . trailingslashit($pathInfo['dirname'])
            . $pathInfo['filename']
            . '.min.'
            . $pathInfo['extension'];

        if (file_exists(trailingslashit(get_template_directory()) . $minifiedTemplate)) {
            return $minifiedTemplate;
        }

        return $template;
    }
}
