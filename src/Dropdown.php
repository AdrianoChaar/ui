<?php

namespace atk4\ui;

class Dropdown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    public function renderView()
    {
        $this->js(true)->dropdown();

        return parent::renderView();
    }
}
