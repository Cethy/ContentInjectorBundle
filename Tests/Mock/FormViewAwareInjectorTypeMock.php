<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Mock;

use Cethyworks\ContentInjectorBundle\Form\AbstractFormViewAwareInjectorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormViewAwareInjectorTypeMock extends AbstractFormViewAwareInjectorType
{
    public function getParent()
    {
        return TextType::class;
    }
}
