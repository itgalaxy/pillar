<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Util\HTML;

class HTMLTest extends \WP_UnitTestCase
{
    public function testRemoveSelfClosingTags()
    {
        $this->assertEquals('foo', HTML::removeSelfClosingTags('foo'));
        $this->assertEquals('<!-- test -->', HTML::removeSelfClosingTags('<!-- test -->'));
        $this->assertEquals('<div></div>', HTML::removeSelfClosingTags('<div></div>'));
        $this->assertEquals(
            '<button type="button">Click Me!</button>',
            HTML::removeSelfClosingTags('<button type="button">Click Me!</button>')
        );
        $this->assertEquals('<br>', HTML::removeSelfClosingTags('<br>'));
        $this->assertEquals('<br>', HTML::removeSelfClosingTags('<br/>'));
        $this->assertEquals('<br>', HTML::removeSelfClosingTags('<br />'));
    }
}
