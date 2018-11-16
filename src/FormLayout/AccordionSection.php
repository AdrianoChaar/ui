<?php

namespace atk4\ui\FormLayout;

class AccordionSection extends \atk4\ui\Accordion
{
    public $section =  null;
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    public function addSection($title, $icon = 'dropdown')
    {
        $this->section = parent::addSection($title, null, $icon);

        return $this->section->add([$this->formLayout, 'form' => $this->form]);
    }

    public function getSection()
    {
        return $this->section();
    }
}