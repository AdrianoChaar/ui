<?php

require 'init.php';

$app->add(['Button', 'View Form input split in Accordion section', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-in-form']);
$app->add(['ui' => 'clearing divider']);

$app->add(['Header', 'Accordion\'s item can be control programmatically.']);

$bar = $app->add(['ui' => 'buttons']);
$b1 = $bar->add(['Button', 'Toggle 1']);
$b2 = $bar->add(['Button', 'Toggle 2']);
$b3 = $bar->add(['Button', 'Toggle 3']);

$app->add(['Header', 'Accordion Items']);

$accordion = $app->add(['Accordion', 'type' => ['styled', 'fluid']]);

$i1 = $accordion->addSection('Static Text');
$i1->add(['Message', 'This content is added on page loaded', 'ui' => 'tiny message']);
$i1->add(['LoremIpsum', 'size' => 1]);

$i2 = $accordion->addSection('Dynamic Text', function ($v) {
    $v->add(['Message', 'Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
    $v->add(['LoremIpsum', 'size' => 2]);
});

$i3 = $accordion->addSection('Dynamic Form', function ($v) {
    $v->add(['Message', 'Loading a form dynamically.', 'ui' => 'tiny message']);
    $f = $v->add(['Form']);
    $f->addField('Email');
    $f->onSubmit(function ($form) {
        return $form->success('Subscribed '.$form->model['Email'].' to newsletter.');
    });
});

// Activate on page load.
$accordion->activate($i2);

$b1->on('click', $accordion->jsToggle($i1));
$b2->on('click', $accordion->jsToggle($i2));
$b3->on('click', $accordion->jsToggle($i3));
