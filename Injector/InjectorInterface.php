<?php

namespace Cethyworks\ContentInjectorBundle\Injector;

interface InjectorInterface
{
    /**
     * @param array $inputIds
     */
    //public function setInputElementIds(array $inputIds);

    /**
     * @param string $contentToInject
     * @return self
     */
    public function inject($contentToInject);

    /**
     * @param string $contentToModify
     * @return string the new content
     */
    public function to($contentToModify);
}
