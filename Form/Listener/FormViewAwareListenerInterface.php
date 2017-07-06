<?php

namespace Cethyworks\ContentInjectorBundle\Form\Listener;

use Cethyworks\ContentInjectorBundle\Listener\ContentInjectorListenerInterface;
use Symfony\Component\Form\FormView;

interface FormViewAwareListenerInterface extends ContentInjectorListenerInterface
{
    /**
     * @param FormView $formView
     */
    public function addFormView(FormView $formView);
}
