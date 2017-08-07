<?php

namespace Cethyworks\ContentInjectorBundle\Command;

class TextCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected $text;

    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->text;
    }
}
