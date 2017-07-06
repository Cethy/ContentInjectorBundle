<?php

namespace Cethyworks\ContentInjectorBundle\Form;

use Cethyworks\ContentInjectorBundle\Form\AbstractFormViewAwareInjectorType;
use Cethyworks\ContentInjectorBundle\Form\Listener\FormViewAwareListenerInterface;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class AbstractFormViewAwareInjectorTypeTest extends TestCase
{
    public function testBuildViewAddFormViewToListener()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|FormView $formView */
        $formView = $this->getMockBuilder(FormView::class)
            ->getMock()
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|FormViewAwareListenerInterface $listener */
        $listener = $this->getMockBuilder(FormViewAwareListenerInterface::class)
            ->getMock()
        ;
        $listener->expects($this->once())
            ->method('addFormView')
            ->with($formView)
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|ListenerRegisterer $registerer */
        $registerer = $this->getMockBuilder(ListenerRegisterer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $registerer->expects($this->once())
            ->method('addListener')
            ->with([$listener, 'onKernelResponse'])
        ;

        /** @var \PHPUnit_Framework_MockObject_MockObject|FormInterface $form */
        $form = $this->getMockBuilder(FormInterface::class)
            ->getMock()
        ;

        /** @var AbstractFormViewAwareInjectorType $formType */
        $formType = $this->getMockForAbstractClass(AbstractFormViewAwareInjectorType::class, [
            $registerer, $listener
        ]);

        $formType->buildView($formView, $form, []);
    }
}
