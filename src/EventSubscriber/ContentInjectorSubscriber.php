<?php

namespace Cethyworks\ContentInjectorBundle\EventSubscriber;

use Cethyworks\ContentInjectorBundle\Injector\InjectorInterface;
use Cethyworks\ContentInjectorBundle\Command\CommandInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ContentInjectorSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandInterface[]
     */
    protected $commands = [];

    /**
     * @var InjectorInterface
     */
    protected $injector;

    public function __construct(InjectorInterface $injector)
    {
        $this->injector = $injector;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    /**
     * $command should implements CommandInterface but can be any callable returning a string
     *
     * @param callable $command
     */
    public function registerCommand(Callable $command)
    {
        $this->commands[] = $command;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // don't inject if not HttpKernelInterface::MASTER_REQUEST
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        // don't inject if :
        // - the response is a redirect
        // - the response content-type is not html
        // - the request did not requested html format
        // - the request is a XmlHttpRequest
        if ('3' === substr($response->getStatusCode(), 0, 1)
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
            || $request->isXmlHttpRequest()
        ) {
            return;
        }

        $this->inject($response);
    }

    /**
     * @param Response $response
     */
    protected function inject(Response $response)
    {
        $oldContent      = $response->getContent();
        $contentToInject = implode("\n", $this->executeCommands());

        $newContent = $this->injector->inject($contentToInject)->to($oldContent);

        $response->setContent($newContent);
    }

    /**
     * @return string[]
     */
    protected function executeCommands()
    {
        return array_map(function(Callable $command) {
            return $command();
        }, $this->commands);
    }
}
