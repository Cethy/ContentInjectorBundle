Cethyworks\ContentInjectorBundle
===
Allow content injection before the app send the response to the client.

Provides factory & abstract classes to build `formType` & other Objects capable of injecting content in Response (javascript for example).

[![CircleCI](https://circleci.com/gh/Cethy/ContentInjectorBundle/tree/master.svg?style=shield)](https://circleci.com/gh/Cethy/ContentInjectorBundle/tree/master)

## Provides
### `kernel.response` Listeners
#### `SimpleContentInjectorListener`
Register a `ContentTransformer` & an `Injector` to inject content into `Response`.
 
#### `SimpleFormViewAwareListener`
Specific listener used with formTypes to pass their `FormViews` to the template injected into `Response`.

@see FormTypes below.

### FormType
#### `AbstractFormViewAwareInjectorType`
Abstract class handling the listener registration.

### Factories
#### `TwigContentInjectorListenerFactory`
Handle the heavy lifting around creating a `SimpleContentInjectorListener` with `TwigContentTransformer`.

Register into services as `cethyworks_content_injector.listener.factory`

@see How to use below.

#### `TwigContentInjectorFormTypeFactory`
Handle the heavy lifting around creating a `AbstractFormViewAwareInjectorType` with `SimpleContentInjectorListener` & `TwigContentTransformer`.

Register into services as `cethyworks_content_injector.form_type.factory`

@see How to use below.


### Test Helpers
#### `FormViewAwareInjectorTypeTestCaseHelper`
Provides some shortcuts to test `AbstractFormViewAwareInjectorType` formTypes.

#### `FormViewAwareInjectorTypeTestCase`
Is a base class to test `AbstractFormViewAwareInjectorType` formTypes.

## How to use
### Registering a listener to inject some content
Create your listener :

    services:
        # Listener
        example.content_injector.listener:
            class: Cethyworks\ContentInjectorBundle\Listener\SimpleContentInjectorListener
            factory: cethyworks_content_injector.listener.factory:createListener
            arguments:
                - "Cethyworks\\ContentInjectorBundle\\Listener\\SimpleContentInjectorListener"
                - "@@ExampleBundle/Resources/assets/twig/template_to_inject.html.twig"

Register the listener into the event_dispatcher (wherever you want) :
    
    $listener = $container->get('example.content_injector.listener');
    /** @var ListenerRegisterer $registerer */
    $registerer = $container->get('cethyworks_content_injector.listener.registerer');
    $registerer->addListener([$listener, 'onKernelResponse']);

@example Cethyworks\GoogleMapDisplayBundle

### Registering a FormViewAwareInjectorType
Create your formView :

    namespace ExampleBundle\Form;
    
    use Cethyworks\ContentInjectorBundle\Form\AbstractFormViewAwareInjectorType;
    
    class ExampleType extends AbstractFormViewAwareInjectorType
    {
        // ...
    }

Register it as a service :

    services:
        # Example Type
        example.type:
            class: ExampleBundle\Form\ExampleType
            factory: cethyworks_content_injector.form_type.factory:createFormType
            arguments:
                - "ExampleBundle\\Form\\ExampleType"
                - "@@ExampleBundle/Resources/assets/twig/template_to_inject.html.twig"
            tags:
                - { name: form.type, alias: example_type }

@example Cethyworks\GooglePlaceAutocompleteBundle
