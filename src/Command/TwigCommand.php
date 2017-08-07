<?php

namespace Cethyworks\ContentInjectorBundle\Command;

use Twig_Environment;

class TwigCommand implements DataAwareCommandInterface, TemplateAwareCommandInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $data;

    /**
     * TwigCommand constructor.
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->twig->render($this->template, $this->data);
    }
}
