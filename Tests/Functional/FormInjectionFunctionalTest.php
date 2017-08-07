<?php

namespace Cethyworks\ContentInjectorBundle\Tests\Functional;

use Cethyworks\ContentInjectorBundle\Tests\Functional\Mock\Form\FooInjectJsType;
use Cethyworks\ContentInjectorBundle\Tests\Functional\Mock\MockKernel;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class FormInjectionFunctionalTest extends WebTestCase
{
    public function testDisplayFormWithInjection()
    {
        // debug=false to be able to use the dispatcher
        // using debug=true, the container dispatcher throws a "LogicException: Event "__section__" is not started." at event dispatch
        $kernel = static::bootKernel(['debug' => false]);
        $container = $kernel->getContainer();

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');

        /** @var FormFactoryInterface $formFactory */
        $formFactory = $container->get('form.factory');

        $form = $formFactory->create(FooInjectJsType::class);
        $formView = $form->createView();

        // trigger kernel.response
        $response = new Response('<body>foo</body>bar');
        $event = new FilterResponseEvent(self::$kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, $response);

        $dispatcher->dispatch('kernel.response', $event);

        $expectedResponseContent = <<<EOF
<body>foo<script>
    var formId = "my_form_id";

    // do something to your form
</script>
</body>bar
EOF;
        $this->assertEquals($expectedResponseContent, $response->getContent());
    }

    /**
     * @return KernelInterface A KernelInterface instance
     */
    protected static function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = MockKernel::class;
        }
        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
