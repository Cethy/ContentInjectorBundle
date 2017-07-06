<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Factory;

use Cethyworks\ContentInjector\Injector\SimpleInjector;
use Cethyworks\ContentInjectorBundle\Factory\TwigContentInjectorListenerFactory;
use Cethyworks\ContentInjectorBundle\Form\Listener\SimpleFormViewAwareListener;
use Cethyworks\ContentInjectorBundle\Listener\SimpleContentInjectorListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use \Twig_Environment;

class TwigContentInjectorListenerFactoryTest extends TestCase
{
    public function dataTestCreateListener()
    {
        return [
            [
                'listenerClassName' => SimpleContentInjectorListener::class,
                'template'      => 'template_mock.html.twig',
            ],
            [
                'listenerClassName' => SimpleFormViewAwareListener::class,
                'template'      => 'template_mock.html.twig',
                'data'          => ['foo' => 'bar']
            ]
        ];
    }

    /**
     * @dataProvider dataTestCreateListener
     */
    public function testCreateListener($listenerClassName, $template, array $data =[])
    {
        $injector = new SimpleInjector();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../data/');
        $twig   = new Twig_Environment($loader);

        $factory = new TwigContentInjectorListenerFactory($injector, $twig, $data);

        $listener = $factory->createListener($listenerClassName, $template);

        $this->assertInstanceOf($listenerClassName, $listener);

        $kernel   = $this->getMockBuilder(HttpKernelInterface::class)
            ->getMock()
        ;
        $response = new Response('foo');
        $event = new FilterResponseEvent($kernel,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );
        $listener->onKernelResponse($event);
    }
}
