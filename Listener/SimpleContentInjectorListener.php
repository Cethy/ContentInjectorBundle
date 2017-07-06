<?php

namespace Cethyworks\ContentInjectorBundle\Listener;

use Cethyworks\ContentInjector\ContentTransformer\ContentTransformerInterface;
use Cethyworks\ContentInjector\Injector\InjectorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SimpleContentInjectorListener implements ContentInjectorListenerInterface
{
    /**
     * @var InjectorInterface
     */
    protected $injector;

    /**
     * @var ContentTransformerInterface
     */
    protected $contentTransformer;

    /**
     * Data passed to ContentTransformerInterface::transform
     *
     * @var array
     */
    protected $data = [];

    /**
     * SimpleGoogleMapDisplayListener constructor.
     *
     * @param ContentTransformerInterface $contentTransformer
     * @param InjectorInterface           $injector
     */
    function __construct(ContentTransformerInterface $contentTransformer, InjectorInterface $injector)
    {
        $this->contentTransformer = $contentTransformer;
        $this->injector           = $injector;
    }

    /**
     * Listen to the kernel.response event
     *
     * @param  FilterResponseEvent $event FilterResponseEvent instance
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
        $response->setContent(
            $this->injector
                ->inject($this->contentTransformer
                    ->transform($this->data))
                ->to($response->getContent())
        );
    }
}
