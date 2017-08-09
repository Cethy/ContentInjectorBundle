<?php

namespace Cethyworks\ContentInjectorBundle\Command\Handler;

use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber;
use Twig_Environment;

class TwigCommandHandler
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var ContentInjectorSubscriber
     */
    protected $injectorSubscriber;

    function __construct(Twig_Environment $twig, ContentInjectorSubscriber $injectorSubscriber)
    {
        $this->twig               = $twig;
        $this->injectorSubscriber = $injectorSubscriber;
    }

    /**
     * @param $template
     * @param array $data
     */
    function registerCommand($template, array $data = [])
    {
        $this->injectorSubscriber->registerCommand((new TwigCommand($this->twig))
            ->setTemplate($template)
            ->setData($data));
    }
}
