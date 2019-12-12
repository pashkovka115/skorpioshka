<?php


namespace Core\Console;


class Module implements Console
{
    public static $commands = [
        'module:remember' => 'Запомнить модуль для последующих команд',
        'module:reset' => 'Забыть всё, что помнил',
    ];
    public static $keys     = [

    ];
    public $remember = '/remember.txt';
    public $current_module;
    protected     $args, $count;


    public function __construct($args = '', $count = '')
    {
        $this->args = $args;
        $this->count = $count;
        $file = MODULES_STORAGE . $this->remember;
        if (file_exists($file)){
            $this->current_module = file_get_contents($file);
        }
    }

    public function remember()
    {
        $color = new Colors();
        if (isset($this->args[1]) and strlen($this->args[1]) > 0) {
            $module_name = MODULES . '/' . $this->args[1];
            if (is_dir($module_name)) {
                file_put_contents(MODULES_STORAGE  . $this->remember, $this->args[1]);
                echo $color->getColoredString('Запомнил', 'green') . "\n";
                return;
            }else{
                echo $color->getColoredString('Нет такого модуля. Обратите внимание на регистр символов.', 'red') . "\n";
                return;
            }
        }
        echo $color->getColoredString('Чего запомнить?', 'red') . "\n";
    }

    public function reset()
    {
        $color = new Colors();
        $files = glob(MODULES_STORAGE. '/*');
        if (count($files) == 0){
            echo $color->getColoredString('А я и не помнил не чего.', 'red') . "\n";
            return;
        }
        foreach ($files as $file){
            unlink($file);
        }
        echo $color->getColoredString('Забыл всё', 'green') . "\n";
    }

    /**
     * @return false|string
     */
    public function getCurrentModuleName()
    {
        return $this->current_module;
    }
}