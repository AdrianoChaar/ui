

.. _callback:

Introduction
---------------------

Agile UI pursues a goal of creating full-featured, interractive user interface. Part of that relies
on abstraction of Browser/Server communication. 

Callback mechanism allow any :ref:`component` of Agile Toolkit to send HTTP requests back to itself
thorugh a unique route and not worry about accidentally affecting or triggering action of any other
component.

One example of this behaviour is the format of :php:meth:`View::on` where you pass 2nd argument as a
PHP callback::

    $button = new Buttion();

    // clicking button generates random number every time
    $button->on('click', function($action){
        return $action->text(rand(1,100));
    });

This creates call-back route transparently which is triggered automatically during the 'click' event.
To make this work seamlessly there are several classes at play. This documentation chapter will walk
you through the callback mechanisms of Agile UI.

The Callback class
------------------

.. php:class:: Callback

Callback is not a View. This class does not extend any other class but it does implement several important
traits:

 - TrackableTrait [todo add link]
 - AppScopeTrait
 - DIContainerTrait

To create a new callback, do this::

    $c = new \atk4\ui\Callback();
    $layout->add($c);

Adding Callback into any object will not affect the rendering, but it will make Callback part of the
:ref:`render_tree` and it will produce it with a unique callback URL:

.. php:method:: getURL($val)

.. php:method:: set

The following example code generates unique URL::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('Callback');
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

If request is sent towards this URL, Callback object can execute a PHP callback, specified through
:php:meth::`Callback::set()`::

    $cb->set(function() use($app) {
        $app->terminate('in callback');
    });

Calling :php:meth:`App::terminate()` will prevent the default behaviour (of rendering UI) and will
output specified string instead.

The callback is triggered just as you call :php:meth:`Callback::set()` and if you return anything
inside the callback, the set() will retutrn it too::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('Callback');
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

    if($cb->set(function(){ return true; })) {
        $label->addClass('red');
    }

.. php:attr:: triggered

You use property `triggered` to detect if callback was executed or not, this way you don't depend
on the callback return value::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('Callback');
    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

    $cb->set(function(){ echo 123; });

    if ($cb->triggered) {
        $label->addClass('red');
    }

If you have passed argument to getURL() the value of this argument will be also asigned to $triggered property.

.. php:attr:: POST_trigger

A Callback class also use a POST variable for triggering. For this case the $callback->name should be set
through the POST data.

Even though the functionality of Callback is very basic, it gives a very solid foundation for number of
derrived classes.

CallbackLater
-------------

.. php::class: CallbackLater

This class is very similar to Callback but it will not execute immediatelly. Instead it will be executed
either at the end at beforeRender or beforeOutput hook from inside App, whichever comes first.

In other words this won't break the flow of your code logic, it simply won't render it. In the next example
the $label->detail is asigned at the very end, yet callback is able to access the property::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('CallbackLater');

    $cb->set(function() use($app, $label) { 
        $app->terminate('Label detail is '.$label->detail);
    });

    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

CallbackLater is used by several actions in Agile UI, such as jsReload(), and ensures that the component
you are reloading are fully rendered by the time callback is executed. 

Given our knowledge of Callbacks, lets take a closer look at how jsReload actually works. So what do we
know about :php:class:`jsReload` already?

 - jsReload is class implementing jsExpressionable
 - you must specify a view to jsReload
 - when triggered, the view will refresh itself on the screen.

Here is example of jsReload::

    $view = $layout->add(['ui'=>'tertiary green inverted segment']);
    $button = $layout->add(['Button', 'Reload Lorem']);

    $button->on('click', new \atk4\ui\jsReload($view));

    $view->add('LoremIpsum');


NOTE: that we can't perform jsReload on LoremIpsum directly, because it's a text, it needs to be inside
a container. When jsReload is created, it transparently creates a 'CallbackLater' object inside
`$view`. On the JavaScript side, it will execute this new route which will respond with a NEW content
for the $view object. 

Should jsReload use regular 'Callback' then it wouldn't know that $view must contain LoremIpsum text.

jsReload existance is only possible thanks to CallbackLater implementation.


jsCallback
----------

.. php::class: jsCallback

So far return value of callback handler was pretty much insignificant. But wouldn't it be great if this
value was meaningfull in some way?

jsCallback implements exactly that. When you specify a handler for jsCallback, it can return one or multiple :ref:`js_action`
which will be rendered into JavaScript in response to triggering callback's URL. Let's bring up our older example, but will
use jsCallback class now::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('jsCallback');

    $cb->set(function() use($app) { 
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->link($cb->getURL());

When you trigger callback, you'll see the output::

    {"success":true,"message":"Success","eval":"alert(\"ok\")"}

This is how jsCallback renders actions and sends them back to the browser. In order to retrieve and execute actions,
you'll need a JavaScript routine. Luckily jsCallback also implements jsExpressionable, so in itself it is an action.

Let me try this again. jsCallback is an :ref:`js_action` which will execute request towards a callback-URL that will
execute PHP method returning one or more :ref:`js_action` which will be received and executed by the original action.

To fully use jsAction above, here is a modified code::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('jsCallback');

    $cb->set(function() { 
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->on('click', $cb);

Now, that is pretty long. For your convenience there is a shorter mechanism::

    $label = $layout->add(['Label', 'Callback test']);

    $label->on('click', function() { 
        return 'ok';
    });

User Confirmation
^^^^^^^^^^^^^^^^^

The implementation perfectly hides existence of callback route, javascript action and jsCallback. The jsCallback
is based on 'Callback' therefore code after :php:meth:`View::on()` will not be executed during triggering.

.. php::attr: confirm

If you set `confirm` property action will ask for user's confirmation before sending a callback::

    $label = $layout->add(['Label','Callback URL:']);
    $cb = $label->add('jsCallback');

    $cb->confirm = 'sure?';

    $cb->set(function() { 
        return 'ok';
    });

    $label->detail = $cb->getURL();
    $label->on('click', $cb);

This is used with delete operations. When using :php:meth:`View::on()` you can pass extra argument to set the 'confirm'
property::

    $label = $layout->add(['Label', 'Callback test']);

    $label->on('click', function() { 
        return 'ok';
    }, ['confirm'=>'sure?']);

JavaScript arguments
^^^^^^^^^^^^^^^^^^^^

.. php::method: set($callback, $arguments = [])

It is possible to modify expression of jsCallback to pass additional arguments to it's callback. The next example
will send browser screen width back to the callback::

    $label = $layout->add('Label');
    $cb = $label->add('jsCallback');

    $cb->set(function($j, $arg1){ 
        return 'width is '.$arg1;
    }, [new \atk4\ui\jsExpression( '$(window).width()' )]);

    $label->detail = $cb->getURL();
    $label->js('click', $cb);

In here you see that I'm using a 2nd argument to $cb->set() to specify arguments which I'd like to fetch from the
browser. Those arguments are passed to the callback and eventually arrive as $arg1 inside my callback. The :php:meth:`View::on()`
also supports argument passing::

    $label = $layout->add(['Label', 'Callback test']);

    $label->on('click', function($j, $arg1) { 
        return 'width is '.$arg1;
    }, ['confirm'=>'sure?', 'args'=>[new \atk4\ui\jsExpression( '$(window).width()' )]]);

If you do not need to specify confirm, you can actually pass arguments in a key-less array too::

    $label = $layout->add(['Label', 'Callback test']);

    $label->on('click', function($j, $arg1) { 
        return 'width is '.$arg1;
    }, [new \atk4\ui\jsExpression( '$(window).width()' )]);


Refering to event origin
^^^^^^^^^^^^^^^^^^^^^^^^

You might have noticed that jsCallback now passes first argument ($j) which so far we have ignored. This argument is a
jQuery chain for the element which received the event. We can change the response to do something with this element.
Instead of `return` use::

    $j->text('width is '.$arg1);

Now instead of showing alert box, label content will be changed to display window width.

There are many other applications for jsCallback, for example, it's used in :php:meth:`Form::onSubmit()`.


VirtualPage
-----------

So far we looked at the callbacks that either return raw output or are linked with JavaScript to execute action.
There is one more interesting way how browser can be connected to PHP - VirtualPage.

.. php::class: VirtualPage

Virtual Page is a view that renders as an empty string, so adding VirtualPage anywhere inside your :ref:`render_tree`
will simply won't display any of it's content anywhere::

    $vp = $layout->add('VirtualPage');
    $vp->add('LoremIpsum');

.. php::attr: $cb

VirtuaPage has a property $cb, which refers to... CallbackLater object! Lets see what happens if we trigger this callback now::

    $vp = $layout->add('VirtualPage');
    $vp->add('LoremIpsum');

    $label = $layout->add('Label');

    $label->detail = $vp->cb->getURL();
    $label->link($vp->cb->getURL());

If you follow the link, you'll see 'LoremIpsum' text, but the label will not be visible now. This is because,
when triggered, VirtualPage will get rid of all the other Content inside layout and will output itself and
any views you have added into VirtualPage object.

Output Modes
^^^^^^^^^^^^

.. php::method: getURL($mode = 'callback')

You may pass argument to :php:meth:`Callback::getURL()` but with VirtualPage this value has a deeper meening.

 - getURL('cut') will return ONLY the HTML of virtual page, no Layout.
 - getURL('popup') will use a very minimalistic layout for valid HTML, suitable for iframes or popup windows.

You can experement with::

    $label->detail = $vp->cb->getURL('popup');
    $label->link($vp->cb->getURL('popup'));

Setting Callback
^^^^^^^^^^^^^^^^

.. php::method: set($callback)

Although VirtualPage works without defining a callback, using one is more reliable and is always recommended::

    $vp = $layout->add('VirtualPage');
    $vp->set(function($vp){ 
        $vp->add('LoremIpsum');
    });

    $label = $layout->add('Label');

    $label->detail = $vp->cb->getURL();
    $label->link($vp->cb->getURL());

Capability of defining callback makes it possible for VirtualPage to be embedded into any :ref:`component`

As example, :php:class:`Tabs` component rely on VirtualPage and allow you to define dynamically loadable tabs::

    $t = $layout->add('Tabs');

    $t->addTab('Tab1')->add('LoremIpsum'); // regular tab
    $t->addTab('Tab2', function($p){ $p->add('LoremIpsum'); }); // dynamic tab

The dynamic tab is implemented through Virtual Page, which is passed to your callback as $p. VirtualPage
is also used in Modal, CRUD and various other components.

.. php::attr: $ui

When using 'popup' mode, the output appears inside a `<div class="ui container">`. If you want to change this
class, you can set $ui property to something else. Try::

    $vp = $layout->add('VirtualPage');
    $vp->add('LoremIpsum');
    $vp->ui = 'red inverted segment';

    $label = $layout->add('Label');

    $label->detail = $vp->cb->getURL('popup');
    $label->link($vp->cb->getURL('popup'));

