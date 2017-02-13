<?php

namespace atk4\ui\Column;

/**
 * Implements Column helper for grid.
 */
class Generic
{
    use \atk4\core\AppScopeTrait;

    public $grid;


    public $attr = [];

    /**
     * Adds a new class to the cells of this column. The optional second argument may be "head",
     * "body", "odd", "even" or "foot". If position is not defined, then class will be applied on all cells.
     */
    public function addClass($class, $position = 'body')
    {
        $this->attr[$position]['class'][] = $class;
    }

    public $class = [];

    /**
     * Adds a new attribute to the cells of this column. The optional second argument may be "head",
     * "body", "odd", "even" or "foot". If position is not defined, then attribute will be applied on all cells.
     *
     * You can also use the "{$name}" value if you wish to specific row value:
     *
     *    $grid->column['name']->setAttr('data', '{$id}');
     *
     */
    public function setAttr($attr, $value, $position = 'body')
    {
        $this->attr[$position][$attr] = $value;
    }

    /**
     * Returns a suitalbe cell tag with the supplied value. Applies modifiers added through addClass and setAttr
     */
    public function getTag($tag, $position, $value)
    {

        $attr = [];

        // "all" applies on all positions
        if($this->attr['all']) {
            $attr = array_merge_recursive($attr, $this->attr['all']);
        }

        // specific position classes
        if($this->attr[$position]) {
            $attr = array_merge($attr, $this->attr[$position]);
        }

        return $this->app->getTag($position=='body'?'td':'th', $attr, $value);
    }


    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Grid. (<th>)
     *
     * Potentialy may include elements for sorting.
     */
    public function getHeaderCell(\atk4\data\Field $f)
    {
        return $this->app->getTag('th', [], $f->getCaption());
    }

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would procude <td> cell when rendered. Example output:
     *
     *   <td><b>{$name}</b></td>
     *
     * The must correspond to the name of the field, although you can also use multiple tags. The tag
     * will also be formatted before inserting, see UI Persistence formatting in the documentation.
     *
     * This method will be executed only once per grid rendering, if you need to format data manually,
     * you should use $this->grid->addHook('formatRow');
     */
    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $this->app->getTag('td', [], '{$'.$f->short_name.'}');
    }

}
