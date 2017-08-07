<?php

namespace Cethyworks\ContentInjectorBundle\Command\Factory;

use Symfony\Component\Form\FormView;

interface FormCommandFactoryInterface
{
    public function create(FormView $formView, array $options);
}
