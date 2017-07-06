<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Factory;


use Cethyworks\ContentInjector\Injector\SimpleInjector;
use Cethyworks\ContentInjectorBundle\Factory\TwigContentInjectorFormTypeFactory;
use Cethyworks\ContentInjectorBundle\Factory\TwigContentInjectorListenerFactory;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;
use Cethyworks\ContentInjectorBundle\Tests\Mock\FormViewAwareInjectorTypeMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class TwigContentInjectorFormTypeFactoryTest extends TestCase
{
    public function dataTestCreateFormType()
    {
        return [
            [
                'typeClassName' => FormViewAwareInjectorTypeMock::class,
                'template'      => 'template_mock.html.twig',
                'data'          => ['foo' => 'bar']
            ]
        ];
    }

    /**
     * @dataProvider dataTestCreateFormType
     */
    public function testCreateFormType($typeClassName, $template, array $data =[])
    {
        $injector = new SimpleInjector();

        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../data/');
        $twig   = new \Twig_Environment($loader);

        $listenerFactory = new TwigContentInjectorListenerFactory($injector, $twig, $data);

        $registerer = new ListenerRegisterer(new EventDispatcher());


        $factory = new TwigContentInjectorFormTypeFactory($listenerFactory, $registerer);

        $formType = $factory->createFormType($typeClassName, $template);

        $this->assertInstanceOf($typeClassName, $formType);

        /** @var \PHPUnit_Framework_MockObject_MockObject|FormInterface $form */
        $form = $this->getMockBuilder(FormInterface::class)
            ->getMock()
        ;

        $formType->buildView(new FormView(), $form, []);
    }
}
