<?php

namespace Cethyworks\ContentInjectorBundle\Registerer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Shortcut to add a listener to the EventDispatcher
 */
class ListenerRegisterer
{
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array [$listener, 'onKernelResponse']
     * @param string $event
     * @param bool $onlyOnce set to false if the listener is allowed to be registered multiple times
     */
    public function addListener($listener, $event = 'kernel.response', $onlyOnce = true)
    {
        // register the listener only one time
        if($onlyOnce && in_array($listener, $this->eventDispatcher->getListeners($event))) {
            return;
        }

        $this->eventDispatcher->addListener($event, $listener);
    }
}
