<?php

namespace Cethyworks\ContentInjectorBundle\Form;

use Cethyworks\ContentInjectorBundle\Form\Listener\FormViewAwareListenerInterface;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Base class to FormTypes which need to inject content relative to their $formView
 */
abstract class AbstractFormViewAwareInjectorType extends AbstractType
{
    /**
     * @var string
     */
    protected $event = 'kernel.response';

    /**
     * @var ListenerRegisterer
     */
    protected $registerer;

    /**
     * @var FormViewAwareListenerInterface
     */
    protected $listener;

    /**
     * AbstractFormViewAwareInjectorType constructor.
     *
     * @param ListenerRegisterer             $registerer
     * @param FormViewAwareListenerInterface $listener
     */
    public function __construct(ListenerRegisterer $registerer, FormViewAwareListenerInterface $listener)
    {
        $this->registerer = $registerer;
        $this->listener   = $listener;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $this->configureAndAddListener($view);
    }


    /**
     * Configure & add listener on the dispatcher
     *
     * @param FormView $view
     */
    protected function configureAndAddListener(FormView $view)
    {
        // register formView to be used by the listener when event dispatched
        $this->listener->addFormView($view);

        $this->registerer->addListener([$this->listener, 'onKernelResponse']);
    }
}
