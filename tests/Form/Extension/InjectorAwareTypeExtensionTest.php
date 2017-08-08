<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Form\Extension;

use Cethyworks\ContentInjectorBundle\Command\Factory\FormCommandFactoryInterface;
use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use Cethyworks\ContentInjectorBundle\Form\Extension\InjectorAwareTypeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InjectorAwareTypeExtensionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormCommandFactoryInterface
     */
    protected $commandFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContentInjectorSubscriber
     */
    protected $subscriber;

    /**
     * @var InjectorAwareTypeExtension
     */
    protected $extension;


    public function testGetExtendedType()
    {
        $this->assertEquals(FormType::class, $this->extension->getExtendedType());
    }

    public function dataTestConfigureOptions()
    {
        return [
            'injector disabled' => [ ['injector' => false], [] ],
            'injector enabled' => [ ['injector' => ['template' => 'foo']], ['injector' => ['template' => 'foo']] ],
        ];
    }

    /**
     * @dataProvider dataTestConfigureOptions
     */
    public function testConfigureOptions($expectedResolvedOptions, $options)
    {
        $resolver = new OptionsResolver();

        $this->extension->configureOptions($resolver);

        $this->assertEquals($expectedResolvedOptions, $resolver->resolve($options));
    }

    public function testBuildViewShouldNotRegisterInjector()
    {
        $options = ['injector' => false];

        $view = new FormView();
        $form = $this->getMockBuilder(FormInterface::class)->disableOriginalConstructor()->getMock();

        $this->commandFactory->expects($this->never())->method('create');
        $this->subscriber->expects($this->never())->method('registerCommand');

        $this->extension->buildView($view, $form, $options);
    }

    public function testBuildViewShouldRegisterInjector()
    {
        $options = ['injector' => ['template' => 'foo']];

        $view = new FormView();
        $form = $this->getMockBuilder(FormInterface::class)->disableOriginalConstructor()->getMock();
        $command = $this->getMockBuilder(TwigCommand::class)->disableOriginalConstructor()->getMock();

        $this->commandFactory->expects($this->once())->method('create')->with($view, $options['injector'])->willReturn($command);

        $this->subscriber->expects($this->once())->method('registerCommand')->with($command);

        $this->extension->buildView($view, $form, $options);
    }

    
    protected function setUp()
    {
        $this->commandFactory = $this->getMockBuilder(FormCommandFactoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->subscriber = $this->getMockBuilder(ContentInjectorSubscriber::class)->disableOriginalConstructor()->getMock();

        $this->extension = new InjectorAwareTypeExtension($this->commandFactory, $this->subscriber);
    }
}
