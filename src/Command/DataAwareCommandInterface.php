<?php

namespace Cethyworks\ContentInjectorBundle\Command;

interface DataAwareCommandInterface extends CommandInterface
{
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data);
}
