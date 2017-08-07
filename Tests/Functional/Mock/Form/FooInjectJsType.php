<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Functional\Mock\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FooInjectJsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'injector' => [ 'template' => '@CethyworksContentInjectorBundle/Tests/Functional/Mock/template/foo_inject_js_type.html.twig' ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'my_form_id';
    }

    public function getParent()
    {
        return TextType::class;
    }
}
