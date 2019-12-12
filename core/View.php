<?php


namespace Core;


use Core\System\Traits\Singleton;

class View
{
    use Singleton;

    protected        $templates     = [];
    protected        $params        = [];
    protected        $vars          = [];
    protected static $system_head   = [];
    protected static $system_footer = [];


    /**
     * @return array
     */
    public function systemHead()
    {
        foreach (self::$system_head as $item) {
            echo $item;
        }
    }

    /**
     * @return array
     */
    public function systemFooter(): array
    {
        foreach (self::$system_footer as $item) {
            echo $item;
        }
    }

    /**
     * @param array $system_head
     */
    public static function setSystemHead($system_head): void
    {
        self::$system_head[] = $system_head;
    }

    /**
     * @param array $system_footer
     */
    public static function setSystemFooter($system_footer): void
    {
        self::$system_footer[] = $system_footer;
    }


    public function render($view, array $params = [])
    {
        ob_start();
        extract($params);
        require $view . '.php';
        $this->templates[$view] = ob_get_clean();
        $this->params[$view] = $params;
    }


    public function getTemplate($name)
    {
        return $this->templates[$name] ?? '';
    }

    public function template($name, array $params = [])
    {
        ob_start();
        extract($params);
        require $name . '.php';
        echo ob_get_clean();
    }

    public function view($name, array $params = [])
    {
        ob_start();
        extract($params);
        require $name . '.php';
        return ob_get_clean();
    }

    public function viewAll($view, array $params = [])
    {
        ob_start();
        if (isset($this->params[$view]) and is_array($this->params[$view])) {
            extract($this->params[$view]);
        }
        extract($this->vars);
        extract($params);
        require $view . '.php';
        return ob_get_clean();
    }


    public function getVar($view, $varName)
    {
        if (isset($this->params[$view][$varName]))
            return $this->params[$view][$varName];
        if (isset($this->vars[$varName]))
            return $this->vars[$varName];
    }


    public function setVar($var, $value): void
    {
        $this->vars[$var] = $value;
    }


    public function setVars(array $vars): void
    {
        $this->vars = $vars;
    }
}



























