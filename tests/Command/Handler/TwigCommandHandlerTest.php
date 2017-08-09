<?php

namespace Cethyworks\ContentInjectorBundle\Command\Handler;

use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class TwigCommandHandlerTest extends TestCase
{
    public function testRegisterCommand()
    {
        $twig = $this->getMockBuilder(Twig_Environment::class)->disableOriginalConstructor()->getMock();
        $injectorSubscriber = $this->getMockBuilder(ContentInjectorSubscriber::class)->disableOriginalConstructor()->getMock();

        $injectorSubscriber->expects($this->once())->method('registerCommand')->with((new TwigCommand($twig))->setTemplate('foo')->setData(['bar' => 'baz']));

        $handler = new TwigCommandHandler($twig, $injectorSubscriber);

        $handler->registerCommand('foo', ['bar' => 'baz']);
    }
}
