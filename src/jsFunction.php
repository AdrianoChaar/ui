<?php

namespace atk4\ui;

/**
 * Implements structure for js closure
 */
class jsFunction implements jsExpressionable {

    public $fx_args;

    public $fx_statements;


    function __construct($args = [], $statements = [])
    {
        $this->fx_args = $args;
        $this->fx_statements = $statements;
    }

    private function _renderArgs($args = [])
    {
        if ($args === null) {
            return [];
        }
        return 
            array_map(function($arg){
                if($arg instanceof jsExpressionable) {
                    return $arg->jsRender();
                }

                return json_encode($arg);
            }, $args);
    }

    function jsRender() {
        return 'function('.join(',',$this->_renderArgs($this->fx_args)).") {\n".
            join(";\n", $this->_renderArgs($this->fx_statements)).";\n".
            "}";
    }
}
