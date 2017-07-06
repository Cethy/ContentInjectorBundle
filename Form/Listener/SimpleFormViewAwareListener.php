<?php

namespace Cethyworks\ContentInjectorBundle\Form\Listener;

use Cethyworks\ContentInjectorBundle\Listener\SimpleContentInjectorListener;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;

class SimpleFormViewAwareListener extends SimpleContentInjectorListener implements FormViewAwareListenerInterface
{
    /**
     * @var FormView[]
     */
    protected $formViews = [];

    /**
     * @param FormView $formView
     */
    public function addFormView(FormView $formView)
    {
        $this->formViews[] = $formView;
    }

    /**
     * Extract input ids from FormViews
     *
     * @param FormView[] $formViews
     *
     * @return string[]
     */
    protected function extractInputIds(array $formViews)
    {
        return array_map(function(FormView $view) {
            return $view->vars['id'];
        }, $formViews);
    }

    /**
     * @param Response $response
     */
    protected function inject(Response $response)
    {
        $this->data = ['input_ids' => $this->extractInputIds($this->formViews)];

        parent::inject($response);
    }
}
