<?php
namespace Itgalaxy\Pillar\Tests;

use Itgalaxy\Pillar\Util\Str;

class StrTest extends \WP_UnitTestCase
{
    public function testRemoveNewLine()
    {
        $this->assertEquals('foo', Str::removeNewLine('foo'));
        $this->assertEquals('foobar', Str::removeNewLine("foo\nbar"));
        $this->assertEquals('foobar', Str::removeNewLine("foo\rbar"));
        $this->assertEquals('foobarfoobar', Str::removeNewLine("foo\nbar\nfoo\rbar"));
    }

    public function testSnake()
    {
        $this->assertEquals('foo', Str::snake('foo'));
        $this->assertEquals('foo_bar', Str::snake('fooBar'));
        $this->assertEquals('foo-bar', Str::snake('FooBar', '-'));
    }
}
