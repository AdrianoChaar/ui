<?php

namespace atk4\ui;

/**
 * Implement a page length selector.
 * Set as a dropdown menu which contains the number of items per page need.
 */
class PageLength extends View
{
    /**
     * The View that will hold this PageLength
     *
     * @var View|null
     */
    public $pageLength = null;

    /**
     * Default page length menu items.
     *
     * @var array
     */
    public $pageLengthItems = [10, 25, 50, 100];

    /**
     * Default button label.
     *  - [ipp] will be replace by the number of pages selected.
     *
     * @var string
     */
    public $label = 'Items per page ([ipp])';

    /**
     * The current number of item per page.
     *
     * @var integer|null
     */
    public $currentIpp = null;

    /**
     * The callback function.
     *
     * @var callable|null
     */
    public $cb = null;

    public function init()
    {
        parent::init();

        if ($this->owner instanceof Menu) {
            $this->pageLength = $this->addClass('ui dropdown');
        } elseif ($this->owner instanceof Paginator) {
            $this->addClass('ui pagination menu');
            $this->pageLength = $this->add('Item')->setElement('a')->addClass('ui item dropdown');
        }

        $menuItems = [];
        foreach ($this->pageLengthItems as $key => $item) {
            $menuItems[] = ['name' => $item, 'value' => $item];
        }

        //Callback later will give us time to properly render menu item before final output.
        $this->cb = $this->add(new CallbackLater());

        //set semantic-ui dropdown onChange function.
        $function = "function(value, text, item){
                            if (value === undefined || value === '' || value === null) return;
                            $(this)
                            .api({
                                on:'now',
                                url:'{$this->cb->getURL()}',
                                data:{ipp:value}
                                }
                            );
                     }";

        //set pageLength as a dropdown.
        $this->pageLength->js(true)->dropdown([
                                         'values'   => $menuItems,
                                         'onChange' => new jsExpression($function),
                                     ]);
        $this->pageLength->set(preg_replace("/\[ipp\]/", $this->currentIpp ? $this->currentIpp : $this->pageLengthItems[0], $this->label));
    }

    /**
     * Run callback when an item is select via dropdown menu.
     * The callback should return a View to be reload after an item
     * has been select.
     *
     * @param null $fx
     */
    public function onPageLengthSelect($fx = null)
    {
        if (is_callable($fx)) {
            if ($this->cb->triggered()) {
                $this->cb->set(function () use ($fx) {
                    $ipp = @$_GET['ipp'];
                    $this->pageLength->set(preg_replace("/\[ipp\]/", $ipp, $this->label));
                    $reload = call_user_func($fx, $ipp);
                    if ($reload) {
                        $this->app->terminate($reload->renderJSON());
                    }
                });
            }
        }
    }
}
