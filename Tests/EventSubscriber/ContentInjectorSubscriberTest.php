<?php

namespace Cethyworks\ContentInjectorBundle\Tests\EventSubscriber;

use Cethyworks\ContentInjectorBundle\Injector\BodyEndInjector;
use Cethyworks\ContentInjectorBundle\Injector\InjectorInterface;
use Cethyworks\ContentInjectorBundle\Command\TextCommand;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ContentInjectorSubscriberTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|HttpKernelInterface
     */
    protected $kernel;


    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::RESPONSE => 'onKernelResponse'],
            ContentInjectorSubscriber::getSubscribedEvents() );
    }

    public function dataTestRegisterCommand()
    {
        return [
            'callable' => [function() { return 'foo';},],
            'command' => [new TextCommand('bar')]
        ];
    }

    /**
     * @dataProvider dataTestRegisterCommand
     * @todo how to test (?)
     */
    public function testRegisterCommand($command)
    {
        $injector = $this->getMockBuilder(InjectorInterface::class)->disableOriginalConstructor()->getMock();
        $subscriber = new ContentInjectorSubscriber($injector);

        $subscriber->registerCommand($command);
    }


    public function dataTestShouldNotInject()
    {
        $xmlHttpRequest = $this->getMockBuilder(Request::class)
            ->getMock()
        ;
        $xmlHttpRequest->expects($this->any())
            ->method('isXmlHttpRequest')
            ->willReturn(true)
        ;

        return [
            'Don\'t inject if not HttpKernelInterface::MASTER_REQUEST' => [
                'request'     => null,
                'requestType' => HttpKernelInterface::SUB_REQUEST,
                'response'    => null ],
            'the response is a redirect' => [
                'request'     => null,
                'requestType' => null,
                'response'    => new Response('', 302) ],
            'the response content-type is not html' => [
                'request'     => null,
                'requestType' => null,
                'response'    => new Response('', 200, [
                    'Content-Type' => 'hNOTtml'
                ]) ],
            'the request did not requested html format' => [
                'request'     => new Request([], [], [
                    '_format' => 'hNOTtml'
                ]),
                'requestType' => null,
                'response'    => null ],
            'the request is a XmlHttpRequest' => [
                'request'     => $xmlHttpRequest,
                'requestType' => null,
                'response'    => null ],
        ];
    }

    /**
     * @dataProvider dataTestShouldNotInject
     */
    public function testShouldNotInject($request = null, $requestType = null, $response = null)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|InjectorInterface $injector */
        $injector = $this->getMockBuilder(InjectorInterface::class)->getMock();
        $injector->expects($this->never())->method('inject');
        $injector->expects($this->never())->method('to');

        $request     = ($request === null     ? new Request() : $request);
        $requestType = ($requestType === null ? HttpKernelInterface::MASTER_REQUEST : $requestType);
        $response    = ($response === null    ? new Response('', 200, ['Content-Type' => 'html']) : $response);

        $event = new FilterResponseEvent($this->kernel, $request, $requestType, $response);

        $subscriber = new ContentInjectorSubscriber($injector);
        $contentBefore = $response->getContent();

        $subscriber->registerCommand(new TextCommand('foo'));

        // transform Response::content
        $subscriber->onKernelResponse($event);

        $this->assertEquals($contentBefore, $response->getContent());
    }

    public function dataTestShouldInject()
    {
        return [
            'simple command' => [
                'expectedContent'         => 'foo</body>',
                'originalResponseContent' => '</body>',
                'commands'                => [new TextCommand('foo')] ],

            'multiple commands' => [
                'expectedContent'         => "foo\nbar\nbaz</body>",
                'originalResponseContent' => '</body>',
                'commands'                => [
                    new TextCommand('foo'),
                    function(){ return 'bar';},
                    new TextCommand('baz'),
                ] ],
        ];
    }

    /**
     * @dataProvider dataTestShouldInject
     */
    public function testShouldInject($expectedContent, $originalResponseContent, array $commands)
    {
        $injector = new BodyEndInjector();

        $response = new Response($originalResponseContent);
        $event = new FilterResponseEvent($this->kernel,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        $subscriber = new ContentInjectorSubscriber($injector);
        foreach ($commands as $command) {
            $subscriber->registerCommand($command);
        }

        // transform Response::content
        $subscriber->onKernelResponse($event);

        $this->assertEquals($expectedContent, $response->getContent());
    }


    protected function setUp()
    {
        $this->kernel = $this->getMockBuilder(HttpKernelInterface::class)->disableOriginalConstructor()->getMock();
    }
}
