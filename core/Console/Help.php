<?php


namespace Core\Console;


class Help implements Console
{
    public static $commands = [
        'help'=>'Справка по коммандам',
        'list'=>'Справка по коммандам',
    ];
    public static $keys = [
        '-h'=>'Справка по коммандам',
        '-help'=>'Справка по коммандам',
    ];

    public $color;


    public function __construct()
    {
        $this->color = new Colors();
    }

    public function help()
    {
        $files = glob(__DIR__. '/*.php');
        foreach ($files as $file){
            require_once $file;
        }
        $commands = [];
        $keys = [];
        $classes = get_declared_classes();
        foreach ($classes as $class){
            $rc = new \ReflectionClass($class);
            if ($rc->implementsInterface('\Core\Console\Console')){
                $props = $rc->getStaticProperties();

                if ($rc->hasProperty('commands') or $rc->hasProperty('keys')){
                    if (isset($props['commands'])){
                        foreach ($props['commands'] as $command => $desc){
                            $commands[$command] = $desc;
                        }
                    }
                    if (isset($props['keys'])){
                        foreach ($props['keys'] as $key=> $des){
                            $keys[$key] = $des;
                        }
                    }
                }

            }

        }
        foreach ($keys as $key => $value){
            echo $this->color->getColoredString($key . ' ----- '. $value, 'white', 'black') . "\n";
        }
        echo  "\n";
        foreach ($commands as $k => $v){
            echo $this->color->getColoredString($k . ' ----- '. $v, 'white', 'black') . "\n";
        }

    }
}