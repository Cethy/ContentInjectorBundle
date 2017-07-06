<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Listener;

use Cethyworks\ContentInjector\ContentTransformer\ContentTransformerInterface;
use Cethyworks\ContentInjector\Injector\InjectorInterface;
use Cethyworks\ContentInjector\Injector\SimpleInjector;
use Cethyworks\ContentInjectorBundle\Listener\SimpleContentInjectorListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SimpleContentInjectorListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContentTransformerInterface
     */
    protected $contentTransformer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|HttpKernelInterface
     */
    protected $kernel;

    protected function setUp()
    {
        $this->contentTransformer = $this->getMockBuilder(ContentTransformerInterface::class)
            ->getMock()
        ;

        $this->kernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->getMock()
        ;
    }

    public function dataTestOnKernelResponseDoesNotInject()
    {
        $xmlHttpRequest = $this->getMockBuilder(Request::class)
            ->getMock()
        ;
        $xmlHttpRequest->expects($this->any())
            ->method('isXmlHttpRequest')
            ->willReturn(true)
        ;

        return [
            // don't inject if not HttpKernelInterface::MASTER_REQUEST
            [
                'request'     => null,
                'requestType' => HttpKernelInterface::SUB_REQUEST,
                'response'    => null
            ],
            // - the response is a redirect
            [
                'request'     => null,
                'requestType' => null,
                'response'    => new Response('', 302)
            ],
            // - the response content-type is not html
            [
                'request'     => null,
                'requestType' => null,
                'response'    => new Response('', 200, [
                    'Content-Type' => 'hNOTtml'
                ])
            ],
            // - the request did not requested html format
            [
                'request'     => new Request([], [], [
                    '_format' => 'hNOTtml'
                ]),
                'requestType' => null,
                'response'    => null
            ],
            // - the request is a XmlHttpRequest
            [
                'request'     => $xmlHttpRequest,
                'requestType' => null,
                'response'    => null

            ],
        ];
    }

    /**
     * @dataProvider dataTestOnKernelResponseDoesNotInject
     */
    public function testOnKernelResponseDoesNotInject($request = null, $requestType = null, $response = null)
    {
        $this->contentTransformer
            ->expects($this->never())
            ->method('transform')
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|InjectorInterface $injector */
        $injector = $this->getMockBuilder(InjectorInterface::class)
            ->getMock()
        ;
        $injector
            ->expects($this->never())
            ->method('inject')
        ;
        $injector
            ->expects($this->never())
            ->method('to')
        ;

        $listener = new SimpleContentInjectorListener($this->contentTransformer, $injector);

        $request     = ($request === null     ? new Request() : $request);
        $requestType = ($requestType === null ? HttpKernelInterface::MASTER_REQUEST : $requestType);
        $response    = ($response === null    ? new Response('', 200, ['Content-Type' => 'html']) : $response);

        $event = new FilterResponseEvent($this->kernel, $request, $requestType, $response);

        $contentBefore = $response->getContent();

        // transform Response::content
        $listener->onKernelResponse($event);

        $this->assertEquals(
            $contentBefore,
            $response->getContent()
        );
    }

    public function dataTestOnKernelResponseDoesInject()
    {
        return [
            [
                'expectedContent'         => 'foo</body>',
                'originalResponseContent' => '</body>',
                'contentToInject'         => 'foo'
            ],
        ];
    }

    /**
     * @dataProvider dataTestOnKernelResponseDoesInject
     */
    public function testOnKernelResponseDoesInject($expectedContent, $originalResponseContent, $contentToInject)
    {
        $this->contentTransformer
            ->expects($this->once())
            ->method('transform')
            ->willReturn($contentToInject)
        ;

        $injector = new SimpleInjector();

        $listener = new SimpleContentInjectorListener($this->contentTransformer, $injector);

        $response = new Response($originalResponseContent);
        $event = new FilterResponseEvent($this->kernel,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        // transform Response::content
        $listener->onKernelResponse($event);

        $this->assertEquals(
            $expectedContent,
            $response->getContent()
        );
    }
}
