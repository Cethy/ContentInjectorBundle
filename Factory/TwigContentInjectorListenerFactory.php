<?php

namespace Cethyworks\ContentInjectorBundle\Factory;

use Cethyworks\ContentInjector\ContentTransformer\TwigContentTransformer;
use Cethyworks\ContentInjector\Injector\InjectorInterface;
use Cethyworks\ContentInjectorBundle\Listener\ContentInjectorListenerInterface;
use \Twig_Environment;

/**
 * Shortcut to build SimpleFormViewAwareListener Listener
 */
class TwigContentInjectorListenerFactory
{
    /**
     * @var InjectorInterface
     */
    protected $injector;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $data;

    public function __construct(InjectorInterface $injector, Twig_Environment $twig, array $data = [])
    {
        $this->injector   = $injector;
        $this->twig       = $twig;

        $this->data       = $data;
    }

    /**
     * @param string $template
     *
     * @return ContentInjectorListenerInterface
     */
    public function createListener($typeClassName, $template)
    {
        return new $typeClassName(
            $this->createTwigContentTransformer($template),
            $this->injector
        );
    }

    /**
     * @param string $template
     *
     * @return TwigContentTransformer
     */
    protected function createTwigContentTransformer($template)
    {
        return new TwigContentTransformer($this->twig, $template, $this->data);
    }
}
