<?php

namespace Ovos\Webpack;

class ManifestTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $src = 'assets/test.js';
        $target = Manifest::resolve($src, __DIR__);
        $this->assertEquals('assets/test.12345.js', $target);
    }
}
