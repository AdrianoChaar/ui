<?php
namespace atk4\ui;

class Button extends View {
    public $_class = 'button';

    public $icon = null;

    function renderView() {
        if ($this->icon) {

            $this->add(new Icon($this->icon), 'Content');

            if ($this->content) {
                $this->addClass('labeled');
                $this->add(new Text($this->content));
                $this->content = false;
            }

            $this->addClass('icon');
            $this->icon = false;
        }

        parent::renderView();
    }

    function recursiveRender() {
        parent::recursiveRender();
        if(!$this->template->get('Content')) {
            $this->template->set('Content', 'Button');
        }
    }
}
