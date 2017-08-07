<?php

namespace Cethyworks\ContentInjectorBundle\Form\Extension;

use Cethyworks\ContentInjectorBundle\Command\Factory\FormCommandFactoryInterface;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InjectorAwareTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ContentInjectorSubscriber
     */
    protected $responseSubscriber;

    /**
     * @var FormCommandFactoryInterface
     */
    protected $commandFactory;

    function __construct(FormCommandFactoryInterface $commandFactory, ContentInjectorSubscriber $responseSubscriber)
    {
        $this->commandFactory     = $commandFactory;
        $this->responseSubscriber = $responseSubscriber;
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    /**
     * Add injector options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('injector', false);

        $resolver->setNormalizer('injector', function (Options $options, $injectorOption) {
            // injector not enabled
            if(! $injectorOption) {
                return false;
            }
            return $injectorOption;
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        // if injector enabled, register it
        if($options['injector']) {
            $this->registerInjector($view, $options['injector']);
        }
    }

    /**
     * Configure & add listener on the dispatcher
     *
     * @param FormView $view
     */
    protected function registerInjector(FormView $view, array $options)
    {
        $command = $this->commandFactory->create($view, $options);

        $this->responseSubscriber->registerCommand($command);
    }
}
