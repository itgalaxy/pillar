<?php
namespace Itgalaxy\Pillar\Base;

abstract class FeatureAbstract implements FeatureInterface
{
    protected $options = [];

    public function __construct($options = [])
    {
        $this->options = array_replace_recursive($this->options, $options);
    }
}
