<?php
namespace atk4\ui;

class Text extends View {
    public $template = false;

    function render() {
        return $this->content;
    }
}
