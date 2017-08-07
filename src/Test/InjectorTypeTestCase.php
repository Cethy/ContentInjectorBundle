<?php

namespace Cethyworks\ContentInjectorBundle\Test;

use Cethyworks\ContentInjectorBundle\Command\Factory\FormCommandFactoryInterface;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use Cethyworks\ContentInjectorBundle\Form\Extension\InjectorAwareTypeExtension;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * @codeCoverageIgnore
 */
class InjectorTypeTestCase extends TypeTestCase
{
    protected $commandFactory;

    protected $subscriber;

    public function setUp()
    {
        $this->commandFactory = $this->getMockBuilder(FormCommandFactoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->subscriber     = $this->getMockBuilder(ContentInjectorSubscriber::class)->disableOriginalConstructor()->getMock();

        parent::setUp();
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions()
    {
        return [
            new InjectorAwareTypeExtension($this->commandFactory, $this->subscriber),
        ];
    }
}
