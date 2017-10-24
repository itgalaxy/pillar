<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class AriaFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('init', [$this, 'addAriaAttributes']);
    }

    public function addAriaAttributes()
    {
        global $allowedposttags;

        foreach ($allowedposttags as $tag => &$allowedPostTag) {
            $allowedPostTag['role'] = true;

            $allowedPostTag['aria-activedescendant'] = true;
            $allowedPostTag['aria-atomic'] = true;
            $allowedPostTag['aria-autocomplete'] = true;
            $allowedPostTag['aria-busy'] = true;
            $allowedPostTag['aria-checked'] = true;
            $allowedPostTag['aria-controls'] = true;
            $allowedPostTag['aria-describedby'] = true;
            $allowedPostTag['aria-disabled'] = true;
            $allowedPostTag['aria-dropeffect'] = true;
            $allowedPostTag['aria-expanded'] = true;
            $allowedPostTag['aria-flowto'] = true;
            $allowedPostTag['aria-grabbed'] = true;
            $allowedPostTag['aria-haspopup'] = true;
            $allowedPostTag['aria-hidden'] = true;
            $allowedPostTag['aria-invalid'] = true;
            $allowedPostTag['aria-label'] = true;
            $allowedPostTag['aria-labelledby'] = true;
            $allowedPostTag['aria-level'] = true;
            $allowedPostTag['aria-live'] = true;
            $allowedPostTag['aria-multiline'] = true;
            $allowedPostTag['aria-multiselectable'] = true;
            $allowedPostTag['aria-orientation'] = true;
            $allowedPostTag['aria-owns'] = true;
            $allowedPostTag['aria-posinset'] = true;
            $allowedPostTag['aria-pressed'] = true;
            $allowedPostTag['aria-readonly'] = true;
            $allowedPostTag['aria-relevant'] = true;
            $allowedPostTag['aria-required'] = true;
            $allowedPostTag['aria-selected'] = true;
            $allowedPostTag['aria-setsize'] = true;
            $allowedPostTag['aria-sort'] = true;
            $allowedPostTag['aria-valuemax'] = true;
            $allowedPostTag['aria-valuemin'] = true;
            $allowedPostTag['aria-valuenow'] = true;
            $allowedPostTag['aria-valuetext'] = true;
        }
    }
}
