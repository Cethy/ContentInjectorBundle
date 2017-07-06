<?php

namespace Cethyworks\ContentInjectorBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

interface ContentInjectorListenerInterface
{
    /**
     * Listen to the kernel.response event
     *
     * @param  FilterResponseEvent $event FilterResponseEvent instance
     */
    public function onKernelResponse(FilterResponseEvent $event);
}
