<?php

namespace atk4\ui;

class App
{
    use \atk4\core\InitializerTrait {
        init as _init;
    }

    public $title = 'Agile UI - Untitled Application';

    public $layout = null; // the top-most view object

    public $template_dir = null;

    public $skin = 'semantic-ui';

    public function __construct($defaults = [])
    {
        if (is_string($defaults)) {
            $defaults = ['title'=>$defaults];
        }

        if (!is_array($defaults)) {
            throw new Exception(['Constructor requires array argument', 'arg' => $defaults]);
        }
        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge(isset($this->$key) && is_array($this->$key) ? $this->$key : [], $val);
            } elseif (!is_null($val)) {
                $this->$key = $val;
            }
        }
    }

    public function initLayout($layout, $options = [])
    {
        if (is_string($layout)) {
            $layout = 'atk4\\ui\\Layout\\'.$layout;
            $layout = new $layout($options);
        }

        $this->html = new View(['defaultTemplate'=>'html.html']);
        $this->layout = $this->html->add($layout);

        return $this;
    }

    public function normalizeClassName($name, $prefix = null)
    {
        if ($name === 'HelloWorld') {
            return 'atk4/ui/HelloWorld';
        }

        return $name;
    }

    public function add()
    {
        return call_user_func_array([$this->layout, 'add'], func_get_args());
    }

    public function run()
    {
        $this->html->template->set('title', $this->title);
        echo $this->html->render();
    }

    public function init()
    {
        $this->_init();
        $this->template_dir = dirname(dirname(__FILE__)).'/template/'.$this->skin;
    }

    public function loadTemplate($name)
    {
        $template = new Template();
        if (in_array($name[0], ['.', '/', '\\'])) {
            $template->load($name);
        } else {
            $template->load($this->template_dir.'/'.$name);
        }

        return $template;
    }

    /**
     * Build a URL that application can use for call-backs.
     *
     * @param array $args List of new GET arguments
     *
     * @return string
     */
    public function url($args = [])
    {
        $url = $_SERVER['REQUEST_URI'];
        $query = parse_url($url, PHP_URL_QUERY);

        $args = http_build_query($args);

        if ($query) {
            $url .= '&'.$args;
        } else {
            $url .= '?'.$args;
        }

        return $url;
    }
}
