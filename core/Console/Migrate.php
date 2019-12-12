<?php


namespace Core\Console;


use Core\Db\Db;

class Migrate implements Console
{
    public static $commands = [
        'migrate' => 'Выполнить миграции',
        'migrate:rollback' => 'Откатить миграции',
        'migrate:refresh' => 'Откатить и выполнить миграции',
    ];
    public static $keys = [
        '--seed' => 'Заполнить БД данными'
    ];
    protected $args, $count;


    public function __construct($args, $count)
    {
        $this->args = $args;
        $this->count = $count;
    }

    public function index()
    {
        $db = new Db();
        $tables = [];
        $query = $db->query('SHOW TABLES');
        foreach ($query as $row) {
            foreach ($row as $item) {
                $tables[] = $item;
            }
        }

        $files = glob(MIGRATIONS_PATH . '/*.php');
        foreach ($files as $file){
            require_once $file;
        }
        $classes = get_declared_classes();
        $color = new Colors();
        $i = 0;
        foreach ($classes as $class) {
            $rc = new \ReflectionClass($class);
            if (in_array(mb_strtolower($rc->getName()), $tables)) {
                continue;
            }
            if ($rc->isUserDefined() and $rc->implementsInterface('\Core\Migrations\Migration')) {
                if ($rc->hasMethod('up')) {
                    echo "\n" . $color->getColoredString('Миграция старт: ' . $class, 'yellow'). "\n";
                    $obj = $rc->newInstance();
                    $method = $rc->getMethod('up');
                    try {
                        $method->invoke($obj);
                    } catch (\ReflectionException $e) {
                        echo $e->getMessage() . "\n";
                    }
                    echo $color->getColoredString('Миграция выполнена: ' . $class, 'green') . "\n";
                    $i++;
                }
            }
        }
        if ($i == 0) {
            echo "\n" . $color->getColoredString('Нет не выполненых миграций', 'red') . "\n\n";
        }
        if (in_array('--seed', $this->args)){
            $db = new \Core\Console\Db($this->args, $this->count);
            $db->seed();
        }
    }


    public function rollback()
    {
        new Db();
        $files = glob(MIGRATIONS_PATH . '/*.php');
        foreach ($files as $file){
            require_once $file;
        }
        $classes = array_reverse(get_declared_classes());
        $color = new Colors();

        foreach ($classes as $class){
            $rc = new \ReflectionClass($class);

            if ($rc->isUserDefined() and $rc->implementsInterface('\Core\Migrations\Migration')){
                if ($rc->hasMethod('down')){

                    echo "\n".$color->getColoredString('Старт отката: ' . $class, 'yellow') ."\n";
                    $obj = $rc->newInstance();
                    $method = $rc->getMethod('down');
                    try{
                        $method->invoke($obj);
                        unset($rc, $obj, $obj, $method);
                    }catch (\ReflectionException $e){
                        echo $e->getMessage() ."\n";
                    }

                    echo $color->getColoredString('Выполнен откат: ' . $class, 'green') ."\n";
                }
            }
        }
    }

    public function refresh()
    {
        $this->rollback();
        $this->index();
    }
}