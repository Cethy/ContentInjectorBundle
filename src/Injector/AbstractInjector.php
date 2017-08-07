<?php

namespace Cethyworks\ContentInjectorBundle\Injector;

abstract class AbstractInjector implements InjectorInterface
{
    /**
     * This marker is used by the injector to know where to inject the new content
     * Watch out for multiple occurrence of this marker
     *
     * @var string
     */
    protected $injectBefore;

    /**
     * @var string
     */
    protected $contentToInject;

    /**
     * @param string $contentToInject
     *
     * @return self
     */
    public function inject($contentToInject)
    {
        $this->contentToInject = $contentToInject;

        return $this;
    }

    /**
     * Inject the new content before the $injectBefore marker
     * Use mb functions if they exists.
     *
     * @param string $contentToModify
     * @return string the new content
     */
    public function to($contentToModify)
    {
        if (function_exists('mb_stripos'))
        {
            $posrFunction = 'mb_strpos';
            $substrFunction = 'mb_substr';
        } // @codeCoverageIgnoreStart
        else
        {
            $posrFunction = 'strpos';
            $substrFunction = 'substr';
        } // @codeCoverageIgnoreEnd

        $pos = $posrFunction($contentToModify, $this->injectBefore);
        if (false !== $pos)
        {
            $contentToModify = $substrFunction($contentToModify, 0, $pos) . $this->contentToInject . $substrFunction($contentToModify, $pos);
        }

        return $contentToModify;
    }
}
