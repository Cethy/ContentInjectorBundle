<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Command;

use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use PHPUnit\Framework\TestCase;

class TwigCommandTest extends TestCase
{
    public function testInvoke()
    {
        $template = 'my_template.html.twig';
        $data = ['foo' => 'bar'];

        $twig = $this->getMockBuilder(\Twig_Environment::class)->disableOriginalConstructor()->getMock();
        $twig->expects($this->once())->method('render')->with($template, $data)->willReturn('foobar');

        $command = (new TwigCommand($twig))
            ->setTemplate($template)
            ->setData($data);


        $this->assertEquals('foobar', $command());
    }
}
