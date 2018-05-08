<?php

require 'init.php';
require 'database.php';

$container = $app->add('View');

$v = $container->add(['View', 'template' => new \atk4\ui\Template('
<div class="ui header">Top countries (alphabetically)</div><ul>
{List}<li class="ui icon label"><i class="{iso}ae{/} flag"></i> {name}andorra{/}</li>{/}
</ul>{$Content}</div>')]);

$l = $v->add('Lister', 'List')->addHook('beforeRow', function ($l) {
    $l->current_row['iso'] = strtolower($l->current_row['iso']);
});

$m = $l->setModel(new Country($db))->setLimit(10);

$ipp = $v->add(new atk4\ui\ItemsPerPageSelector(['label' => 'Select how many countries:']), 'Content');

$ipp->onPageLengthSelect(function ($ipp) use ($m, $container) {
    $m->setLimit($ipp);

    return $container;
});
