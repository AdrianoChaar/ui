<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\UserAction\Action;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\jsUserAction;
use atk4\ui\ActionExecutor\UserAction;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class CRUD extends Grid
{
    /** @var array of fields to show */
    public $fieldsDefault = null;

    /** @var array Default action to perform when adding or editing is successful * */
    public $notifyDefault = ['jsToast', 'settings'=> ['message' => 'Data is saved!', 'class' => 'success']];

    public $jsExecutor = jsUserAction::class;
    public $executor = UserAction::class;

    /**
     * Sets data model of CRUD.
     *
     * @param \atk4\data\Model $m
     * @param array            $defaultFields
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        parent::setModel($m);

        $this->model->unload();

        foreach ($m->getActions(Generic::SINGLE_RECORD) as $single_record_action) {
            $executor = $this->factory($this->getActionExecutor($single_record_action));
            $single_record_action->fields = ($executor instanceof jsUserAction) ? false : $this->fieldsDefault ?? true;
            $single_record_action->ui['executor'] = $executor;
            $executor->addHook('afterExecute', function ($x, $m, $id) {
                if ($m->loaded()) {
                    $js = $this->jsSave($this->notifyDefault);
                } else {
                    $js = $this->jsDelete();
                }

                return $js;
            });
            $this->addAction($single_record_action);
        }

        foreach ($m->getActions(Generic::NO_RECORDS) as $single_record_action) {
            $executor = $this->factory($this->getActionExecutor($single_record_action));
            $single_record_action->fields = ($executor instanceof jsUserAction) ? false : $this->fieldsDefault ?? true;
            $single_record_action->ui['executor'] = $executor;
            $executor->addHook('afterExecute', function ($x, $m, $id) {
                if ($m->loaded()) {
                    $js = $this->jsSave($this->notifyDefault);
                }

                return $js;
            });
            $btn = $this->menu->addItem(['Add new '.$this->model->getModelCaption(), 'icon' => 'plus']);
            $btn->on('click.atk_CRUD', $single_record_action, [$this->name.'_sort' => $this->getSortBy()]);
        }

        return $this->model;
    }

    protected function getActionExecutor($action)
    {
        $executor = (!$action->args && !$action->fields && !$action->preview) ? $this->jsExecutor : $this->executor;

        return $this->factory($executor);
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        parent::applySort();
    }

    /**
     * Default js action when saving form.
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    public function jsSave($notifier)
    {
        return [
            $this->factory($notifier, null, 'atk4\ui'),
            // reload Grid Container.
            $this->container->jsReload([$this->name.'_sort' => $this->getSortBy()]),
        ];
    }

    public function jsDelete()
    {
        return (new jQuery())->closest('tr')->transition('fade left');
    }
}
