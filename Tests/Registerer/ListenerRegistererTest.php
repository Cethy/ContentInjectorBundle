<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Registerer;

use Cethyworks\ContentInjectorBundle\Listener\ContentInjectorListenerInterface;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ListenerRegistererTest extends TestCase
{
    public function testAddListenerOnlyOnce()
    {
        $eventDispatcher = new EventDispatcher();

        $registerer = new ListenerRegisterer($eventDispatcher);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ContentInjectorListenerInterface $listener */
        $listener = $this->getMockBuilder(ContentInjectorListenerInterface::class)
            ->getMock()
        ;

        $this->assertEquals([], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo']);
        $this->assertEquals([[$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo']);
        $this->assertEquals([[$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo']);
        $this->assertEquals([[$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));
    }

    public function testAddListenerMultipleTime()
    {
        $eventDispatcher = new EventDispatcher();

        $registerer = new ListenerRegisterer($eventDispatcher);

        /** @var \PHPUnit_Framework_MockObject_MockObject|ContentInjectorListenerInterface $listener */
        $listener = $this->getMockBuilder(ContentInjectorListenerInterface::class)
            ->getMock()
        ;

        $this->assertEquals([], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo'], 'kernel.response', false);
        $this->assertEquals([[$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo'], 'kernel.response', false);
        $this->assertEquals([[$listener, 'foo'], [$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));

        $registerer->addListener([$listener, 'foo'], 'kernel.response', false);
        $this->assertEquals([[$listener, 'foo'], [$listener, 'foo'], [$listener, 'foo']], $eventDispatcher->getListeners('kernel.response'));
    }
}
