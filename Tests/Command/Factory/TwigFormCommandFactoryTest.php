<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Command\Factory;

use Cethyworks\ContentInjectorBundle\Command\Factory\TwigFormCommandFactory;
use Cethyworks\ContentInjectorBundle\Command\TwigCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormView;

class TwigFormCommandFactoryTest extends TestCase
{
    public function testCreateShouldThrowLogicException()
    {
        $this->setExpectedException(\LogicException::class, '$options[\'template\'] must be given.');


        $twig = $this->getMockBuilder(\Twig_Environment::class)->disableOriginalConstructor()->getMock();

        $factory = new TwigFormCommandFactory($twig);

        $factory->create(new FormView(), []);
    }

    public function testCreate()
    {
        $formView = new FormView();
        $options = ['template' => 'my_template.html.twig'];


        $expectedTemplate = $options['template'];
        $expectedData     = ['form_view' => $formView];


        $twig = $this->getMockBuilder(\Twig_Environment::class)->disableOriginalConstructor()->getMock();
        $twig->expects($this->once())->method('render')
            ->with($expectedTemplate, $expectedData)->willReturn('foobar');

        $factory = new TwigFormCommandFactory($twig);

        $command = $factory->create($formView, $options);

        $this->assertInstanceOf(TwigCommand::class, $command);
        $this->assertEquals('foobar', $command());
    }
}
