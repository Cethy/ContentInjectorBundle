<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Command;

use Cethyworks\ContentInjectorBundle\Command\TextCommand;
use PHPUnit\Framework\TestCase;

class TextCommandTest extends TestCase
{
    public function testInvoke()
    {
        $command = new TextCommand('foo');

        $this->assertEquals('foo', $command());
    }
}
