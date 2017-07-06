<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Form\Listener;

use Cethyworks\ContentInjector\ContentTransformer\ContentTransformerInterface;
use Cethyworks\ContentInjector\Injector\SimpleInjector;
use Cethyworks\ContentInjectorBundle\Form\Listener\SimpleFormViewAwareListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SimpleFormViewAwareListenerTest extends TestCase
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

    public function testOnKernelResponseInjectFormId()
    {
        /** @var FormView $formView */
        $formView = new FormView();
        $formView->vars['id'] = 'form_id';

        $this->contentTransformer
            ->expects($this->once())
            ->method('transform')
            ->with(['input_ids' => ['form_id']])
        ;

        $injector = new SimpleInjector();

        $listener = new SimpleFormViewAwareListener($this->contentTransformer, $injector);

        $listener->addFormView($formView);


        $response = new Response('foo');
        $event = new FilterResponseEvent($this->kernel,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        // transform Response::content
        $listener->onKernelResponse($event);
    }

    public function testOnKernelResponseInjectMultipleFormId()
    {
        $this->contentTransformer
            ->expects($this->once())
            ->method('transform')
            ->with(['input_ids' => ['form_id', 'form_id_2', 'form_id_3']])
        ;

        $injector = new SimpleInjector();

        $listener = new SimpleFormViewAwareListener($this->contentTransformer, $injector);

        /** @var FormView $formView */
        $formView = new FormView();
        $formView->vars['id'] = 'form_id';

        $listener->addFormView($formView);

        /** @var FormView $formView */
        $formView = new FormView();
        $formView->vars['id'] = 'form_id_2';

        $listener->addFormView($formView);


        /** @var FormView $formView */
        $formView = new FormView();
        $formView->vars['id'] = 'form_id_3';

        $listener->addFormView($formView);

        $response = new Response('foo');
        $event = new FilterResponseEvent($this->kernel,
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $response
        );

        // transform Response::content
        $listener->onKernelResponse($event);
    }
}
