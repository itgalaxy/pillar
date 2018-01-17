<?php
namespace Itgalaxy\Pillar\Feature;

use Itgalaxy\Pillar\Base\FeatureAbstract;

class HideVersionFeature extends FeatureAbstract
{
    protected $options = [
        'salt' => null,
        'hideVersionQueryParam' => false
    ];
    /**
     * Register our class method(s) with the appropriate WordPress hooks.
     *
     * @return void
     */
    public function initialize()
    {
        if (has_action('wp_head', 'wp_generator') !== false) {
            remove_action('wp_head', 'wp_generator');
        }

        add_filter(
            'the_generator',
            function () {
                return '';
            }
        );

        add_filter('script_loader_src', [$this, 'removeVersionQueryParam']);
        add_filter('style_loader_src', [$this, 'removeVersionQueryParam']);
    }

    public function removeVersionQueryParam($src)
    {
        parse_str(wp_parse_url($src, PHP_URL_QUERY), $query);

        if (!empty($query['ver'])) {
            $src = remove_query_arg('ver', $src);

            if (!$this->options['hideVersionQueryParam']) {
                $salt = isset($this->options['salt']) ? $this->options['salt'] : null;

                if ($salt === null && defined('NONCE_SALT')) {
                    $salt = NONCE_SALT;
                }

                $ver = $query['ver'];
                $data = 'pillar_plugin|ver_query_var|' . $ver . '|' . $salt;
                $hash = null;

                if (function_exists('hash')) {
                    $hash = hash('sha256', $data);
                } else {
                    $hash = sha1($data);
                }

                $src = add_query_arg(['ver' => $hash], $src);
            }
        }

        return $src;
    }
}
