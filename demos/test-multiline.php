<?php

require 'init.php';
require 'database.php';

//class Client extends atk4\data\Model
//{
//    public $table = 'client';
//    public $caption = 'Client';
//
//    public function init()
//    {
//        parent::init();
//
//        $data = [];
//
//        $this->addField('name');
//
//        //TODO not working because ref ContainsMany has no ui reference.
//        //$this->containsMany('Addresses', [Address::class, 'ui' => ['form' => ['MultiLine', 'caption' => 'ml']]]);
//        //$this->containsMany('Addresses', [Address::class, 'system' => false]);
//        //$this->containsMany('Addresses', [new Address(new \atk4\data\Persistence\Array_($data)), 'system' => false, /*'ui' => ['form' => ['MultiLine']]*/]);
//    }
//}
//
//class Address extends atk4\data\Model
//{
//    public $table = 'address';
//    public $caption = 'Address';
//
//    public function init()
//    {
//        parent::init();
//
//        $this->addField('street');
//        $this->addField('city');
//        $this->addField('country');
//        $this->addField('postal_code');
//    }
//}

$m = new Client($db);
//$m = new Country($db);
//$m->load(1);

$f = $app->add('Form');
//$f->addField('test');
$f->setModel($m);

//$f->onSubmit(function($f) {
//    $f->layout->getElement('Addresses')->saveRows();
//    $f->model->save();
//});

//exports DB data without typecasting - to see what's actually stored in DB
//$app->add('View')->setElement('pre')->addStyle(['border'=>'1px solid blue', 'overflow'=>'auto'])
//    ->set(var_export($m->export(null, null, false), true));
