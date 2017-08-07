Cethyworks\ContentInjectorBundle
===
Allow effective content injection before the app sends the response to the client.

It uses a global subscriber which will inject content from collected `InjectorCommands` when `kernel.response` event is fired.
The `InjectorCommands` can be a simple `callable` returning a string or be as complex as a rendered twig template with data.

The bundle provides helpers to inject simple text, twig templates and `FormView` aware commands.

[![CircleCI](https://circleci.com/gh/Cethy/ContentInjectorBundle/tree/master.svg?style=shield)](https://circleci.com/gh/Cethy/ContentInjectorBundle/tree/master)

## Install

    composer require cethyworks/content-injector-bundle

`AppKernel.php`

	class AppKernel extends Kernel
	{
		registerBundles()
		{
			return [
				// ...
				new Cethyworks\ContentInjectorBundle\CethyworksContentInjectorBundle()
			];
		}
	}

## How to use
The global subscriber is configured out of the box.

You just need to register one or more `InjectorCommand` :

    $subscriber = $container->get(ContentInjectorSubscriber::class);
	$subscriber->regiterCommand(function(){ return 'inject_me'; });
	
### With twig template

	$command = ( new TwigCommand($container->get('twig')) )
		->setTemplate('@AppBundle\Resources/assets/twig/foo.html.twig')
		->setData(['foo' => 'bar']);
    $subscriber = $container->get(ContentInjectorSubscriber::class)->regiterCommand($command);


### With FormType
The bundle provides a `TypeExtension` "extending" `FormType` (virtually all forms) adding a `injector` option allowing the configuration of an injector aware of the FormType's `FormView`. It ca be used like this :

`AppInjectJsType.php`

	class AppInjectJsType extends AbstractType
    {
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults(array(
                'injector' => [ 
                	'template' => '@AppBundle/Resources/assets/twig/app_inject_js_type.html.twig' ]
            ));
        }
    
        public function getBlockPrefix()
        {
            return 'my_form_id';
        }
    
        public function getParent()
        {
            return EntityType::class;
        }
    }

`app_inject_js_type.html.twig`	

	<script>
        var formId = "{{ form_view.vars['id'] }}";
    
        // do something to your form
    </script>




## What's in the box ?
### EventSubscriber
- `Cethyworks\ContentInjectorBundle\EventSubscriber\ContentInjectorSubscriber($injector)`

Collects `InjectorCommands`, execute and inject them into the `Response` when `kernel.response` event is fired.

### Commands
- `Cethyworks\ContentInjectorBundle\Command\CommandInterface`

Command interface.


- `Cethyworks\ContentInjectorBundle\Command\TextCommand($text)`

Simple Text Command.


- `Cethyworks\ContentInjectorBundle\Command\TwigCommand($twig)->setTemplate($template)->setData($data)`

Twig Command, render `$template` with `$data`.


### FormExtension
- `Cethyworks\ContentInjectorBundle\Form\Extension\InjectorAwareTypeExtension($commandFactory, $responseSubscriber)`

Enable the `injector` form option.

@see section **How to / With Form" ""Type** above.


### Factories
- `Cethyworks\ContentInjectorBundle\Command\Factory\TwigFormCommandFactory`

Used internally by `InjectorAwareTypeExtension`, create TwigCommands aware of `FormView`.


### Injectors
- `Cethyworks\ContentInjectorBundle\Injector\InjectorInterface`

Injector interface.

- `Cethyworks\ContentInjectorBundle\Injector\BodyEndInjector`

Injects just before `</body>` tag.

### Test helper
???

### todo
- Custom `@inject` annotation (?)


### Test Helpers
#### `FormViewAwareInjectorTypeTestCaseHelper`
Provides some shortcuts to test `AbstractFormViewAwareInjectorType` formTypes.

#### `FormViewAwareInjectorTypeTestCase`
Is a base class to test `AbstractFormViewAwareInjectorType` formTypes.
