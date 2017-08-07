<?php

namespace Cethyworks\ContentInjectorBundle\Command;

interface CommandInterface
{
    /**
     * @return string
     */
    public function __invoke();
}
