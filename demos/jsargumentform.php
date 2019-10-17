<?php

require 'init.php';
require 'database.php';

use atk4\ui\ActionExecutor\jsArgumentForm;
use atk4\ui\Form;
$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set(12);

$country = new Country($db);

// clicking button should simply display toast ok
$country->addAction('test1', ['callback'=> function () {
    return 'ok';
}]);

// clicking button should show preview window wiht OK. If OK is pressed should close window and display toast OK
$country->addAction('test2', ['preview'=> function () {
    return 'show this on preview screen';
}, 'callback'=>function () {
    return 'ok';
}]);

// clicking button no effect, because action is disabled
$country->addAction('test3', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$country->addAction('test4', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m) {
    return 'age=';
}]);

// user can edit 'iso3' field before action is invoked (will be blank, since it's not loaded but should show proper label). NOT SAVING! but will still show 'ok' in toast
$country->addAction('test5', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);

// if action throws exception, need to properly display to user
$country->addAction('test6', ['callback'=> function () {
    throw new \atk4\ui\Exception('ouch');
}]);

$country->addAction('do_all', ['args'=> ['age'=>['type'=>'integer', 'required'=> true]], 'fields'=> ['iso3'], 'callback'=> function () {
    return 'ok';
}, 'preview'=> function () {
    return 'show this on preview screen';
}]);

// action may require confirmation, before activating
//$country->addAction('test7', ['confirm'=>'Call action?', 'callback'=>function(){ return 'ok'; }]);

$buttons = $app->add(['ui'=>'vertical basic buttons']);

$my_action = $country->getAction('do_all');
$ex = $app->add(new \atk4\ui\ActionExecutor\UserAction())->setAction($my_action);
$ex->assignTrigger($buttons->add(['Button', $my_action->getDescription()]), [$ex->name => $field->jsInput()->val()]);

//foreach ($country->getActions() as $action) {
//
//    $ex = $app->add(new \atk4\ui\ActionExecutor\UserAction())->setAction($action);
//    $ex->assignTrigger($buttons->add(['Button', $action->getDescription()]));
//    //$buttons->add(['Button', $action->getDescription()])->on('click', $ex->jsTrigger());
//}

//$field = $form->addField('age');
//$button->on('click', jsAction($country->getAction('test4'), ['age'=>$field->jsInput()->js()->val()]));

/*
$app->add(new \atk4\ui\Header(['Enter Country model id', 'size' => 4]));
$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set(12);
>>>>>>> 0203a7c1d6f6ccb69edcb6762dd18401c7c54290

//$app->add(new \atk4\ui\Header(['Enter Country model id', 'size' => 4]));
//$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set(12);
//
//$btn_edit = $app->add(['Button', 'Edit']);
//$btn_add = $app->add(['Button', 'Add New']);
//
//$vp_edit = $app->add(['VirtualPage']);
//$vp_add = $app->add(['VirtualPage']);
//
//$form = new Form();
//$form->addHook('formInit', function ($f) {
//    // setup special form content.
//});
//
//$btn_edit->on('click', new jsArgumentForm($country->getAction('edit'), $vp_edit, $field->jsInput()->val(), $form));
//$btn_add->on('click', new jsArgumentForm($country->getAction('add'), $vp_add));
*/