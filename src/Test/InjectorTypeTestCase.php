<?php

namespace Cethyworks\ContentInjectorBundle\Test;

use Cethyworks\ContentInjectorBundle\Command\Factory\FormCommandFactoryInterface;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use Cethyworks\ContentInjectorBundle\Form\Extension\InjectorAwareTypeExtension;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @codeCoverageIgnore
 */
class InjectorTypeTestCase extends TypeTestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|FormCommandFactoryInterface */
    protected $commandFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContentInjectorSubscriber */
    protected $subscriber;

    public function setUp()
    {
        $this->commandFactory = $this->getMockBuilder(FormCommandFactoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->subscriber     = $this->getMockBuilder(ContentInjectorSubscriber::class)->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    /**
     * @return FormTypeExtensionInterface[]
     */
    protected function getTypeExtensions()
    {
        return [
            new InjectorAwareTypeExtension($this->commandFactory, $this->subscriber),
        ];
    }
}
