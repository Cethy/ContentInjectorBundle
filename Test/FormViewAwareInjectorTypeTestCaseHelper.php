<?php

namespace Cethyworks\ContentInjectorBundle\Test;

use Cethyworks\ContentInjectorBundle\Form\Listener\FormViewAwareListenerInterface;
use Cethyworks\ContentInjectorBundle\Registerer\ListenerRegisterer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\AbstractType;

class FormViewAwareInjectorTypeTestCaseHelper
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     *
     * @return ListenerRegisterer|\PHPUnit_Framework_MockObject_MockObject
     */
    public static function getRegisterer(\PHPUnit_Framework_TestCase $testCase)
    {
        $registerer = new ListenerRegisterer(new EventDispatcher());

        return $registerer;
    }

    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     *
     * @return FormViewAwareListenerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public static function getListenerMock(\PHPUnit_Framework_TestCase $testCase)
    {
        return $testCase->getMockBuilder(FormViewAwareListenerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    /**
     * @param string[]                       $formTypes
     * @param ListenerRegisterer             $registerer
     * @param FormViewAwareListenerInterface $listener
     *
     * @return AbstractType[]
     */
    public static function buildPreloadedTypes($formTypes, ListenerRegisterer $registerer, FormViewAwareListenerInterface $listener)
    {
        return array_map(function($type) use ($registerer, $listener) {
            return new $type($registerer, $listener);
        }, $formTypes);
    }
}
