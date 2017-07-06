<?php

namespace Cethyworks\ContentInjectorBundle\Test;

use Cethyworks\ContentInjectorBundle\Form\Listener\FormViewAwareListenerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Shortcut to test FormTypes extending AbstractFormViewAwareInjectorType
 */
abstract class FormViewAwareInjectorTypeTestCase extends TypeTestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var FormBuilder
     */
    protected $builder;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var FormViewAwareListenerInterface
     */
    protected $listener;

    public function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->registerer = FormViewAwareInjectorTypeTestCaseHelper::getRegisterer($this);
        $this->listener   = FormViewAwareInjectorTypeTestCaseHelper::getListenerMock($this);

        $this->factory    = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->getFormFactory()
        ;
        $this->builder    = new FormBuilder(null, null, $this->dispatcher, $this->factory);

        parent::setUp();
    }

    /**
     * Must return all the FormTypes needing the EntityManager in their constructor
     * They will be passed to the PreloadExtension
     *
     * @see self::getExtensions()
     *
     * @return string[]
     */
    abstract protected function getTypeClassNames();

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions()
    {
        return [
            // register the type instances with the PreloadedExtension
            new PreloadedExtension(FormViewAwareInjectorTypeTestCaseHelper::buildPreloadedTypes(
                $this->getTypeClassNames(),
                $this->registerer,
                $this->listener
            ), []),
        ];
    }
}
