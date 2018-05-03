<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class RemoveBlogPageMenuItemClassesFeature extends FeatureAbstract
{
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        add_filter('nav_menu_css_class', [$this, 'fixBlogPageClasses'], 10, 2);
    }

    public function fixBlogPageClasses($classes, $item)
    {
        if (is_category() || is_tax() || !get_the_ID() || is_page() || is_singular('post') || is_home()) {
            return $classes;
        }

        $postID = get_the_ID();

        if (!$postID) {
            return $classes;
        }

        $postTypeName = get_post_type($postID);

        if (!$postTypeName) {
            return $classes;
        }

        $postTypeObject = get_post_type_object($postTypeName);

        if (!$postTypeObject) {
            return $classes;
        }

        $postTypeSlug = $postTypeObject->rewrite['slug'];
        $menuSlug = mb_strtolower(trim($item->url));

        if (mb_strpos($menuSlug, $postTypeSlug) !== false) {
            $classes[] = 'current-menu-item';
        } else {
            $classes = array_diff($classes, ['current_page_parent', 'active']);
        }

        return $classes;
    }
}
