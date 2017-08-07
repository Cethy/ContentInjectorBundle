<?php

namespace Cethyworks\ContentInjectorBundle\Command;

interface TemplateAwareCommandInterface extends CommandInterface
{
    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template);
}
