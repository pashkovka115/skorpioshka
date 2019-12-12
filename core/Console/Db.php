<?php


namespace Core\Console;


class Db implements Console
{
    public static $commands = [
        'db:seed'=>'Посев БД',
    ];
    public static $keys = [
        '--table=ClassName'=>'Заполнить данными эту таблицу'
    ];

    protected $args, $count;


    public function __construct($args, $count)
    {
        $this->count = $count;
        $this->args = $args;
        new \Core\Db\Db();
    }


    public function seed()
    {
        $color = new Colors();


        if (isset($this->args[1]) and strpos($this->args[1], '--table=') !== false){
            $sp = explode('=', $this->args[1]);
            if (isset($sp[1])){
                $class = $sp[1];
                echo $color->getColoredString('Сеем: ' . $class, 'yellow') . "\n";
                require_once SEED_PATH . '/'.$class . '.php';
                $obj = new $class();

                $obj->run();
                echo $color->getColoredString('Засеяли: ' . $class, 'green') . "\n";
                return;
            }
        }

        require_once SEED_PATH . '/DatabaseSeeder.php';
        $db = new \DatabaseSeeder();
        $classes = $db->seeds;

        foreach ($classes as $class){
            echo "\n" . $color->getColoredString('Старт посева: ' . $class, 'yellow') . "\n";
            require_once SEED_PATH . '/' .$class . '.php';
            $obj = new $class();
            $obj->run();
            echo $color->getColoredString('Засеено: ' . $class, 'green') . "\n";
        }
    }
}

















