<?php

namespace Cethyworks\ContentInjectorBundle\Factory;

use Cethyworks\ContentInjectorBundle\Form\AbstractFormViewAwareInjectorType;
use Cethyworks\ContentInjectorBundle\Form\Listener\SimpleFormViewAwareListener;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;

/**
 * Shortcut to build AbstractFormViewAwareInjectorType FormTypes
 */
class TwigContentInjectorFormTypeFactory
{
    /**
     * @var ListenerRegisterer
     */
    protected $registerer;

    /**
     * @var TwigContentInjectorListenerFactory
     */
    protected $listenerFactory;

    public function __construct(TwigContentInjectorListenerFactory $listenerFactory, ListenerRegisterer $registerer)
    {
        $this->listenerFactory = $listenerFactory;
        $this->registerer      = $registerer;
    }


    /**
     * @param string $typeClassName
     * @param string $template
     *
     * @return AbstractFormViewAwareInjectorType
     */
    public function createFormType($typeClassName, $template)
    {
        return new $typeClassName(
            $this->registerer,
            $this->listenerFactory->createListener(SimpleFormViewAwareListener::class, $template)
        );
    }
}
