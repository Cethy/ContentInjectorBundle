<?php

namespace Cethyworks\ContentInjectorBundle\Command\Factory;

use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use Symfony\Component\Form\FormView;
use Twig_Environment;

class TwigFormCommandFactory implements FormCommandFactoryInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * TwigFormCommandFactory constructor.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param FormView $formView
     * @param array $options
     * @return Callable
     */
    public function create(FormView $formView, array $options)
    {
        if(!isset($options['template'])) {
            throw new \LogicException('$options[\'template\'] must be given.');
        }

        return (new TwigCommand($this->twig))
            ->setTemplate($options['template'])
            ->setData(['form_view' => $formView]);
    }
}
