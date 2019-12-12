<?php


namespace Core\Console;



use Core\Console\Traits\Helper;

class Make implements Console
{
    use Helper;

    public static $commands       = [
        'make:migrate' => 'Создать миграцию',
        'make:module' => 'Создать новый модуль',
        'make:model' => 'Создать модель',
        'make:controller' => 'Создать контроллер',
        'make:seed' => 'Создать засеиватель БД'
    ];
    public static $keys           = [
        '--module=moduleName' => 'Для модуля'
    ];
    public        $current_module = false;
    protected     $color;
    protected     $count, $table, $config;


    public function __construct($args, $count)
    {
        $this->count = $count;
        $this->color = new Colors();
        if (isset($args[1])) {
            $this->table = $args[1];
        } else {
            echo $this->color->getColoredString('Чего хотел?', 'red') . "\n";
            exit();
        }
        $module = new Module();
        $this->current_module = $module->getCurrentModuleName();

        array_shift($args);
        array_shift($args);
        $this->config = $args;
        new \Core\Db\Db();
    }

    public function migrate()
    {
//        print_r($this->config);
//        $file = date('d_m_Y') . '_' . time() . '_' . $this->table . '.php';
        $file = date('Y_m_d') . '_' . time() . '_' . $this->table . '.php';
        $table = ucfirst($this->table);
        $template_table = <<<HERE
<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class $table implements \Core\Migrations\Migration {
    public function up()
    {
        Capsule::schema()->create('$this->table', function (Blueprint \$table){
        \$table->increments('id');
        
        \$table->timestamps();
});
    }
    
    
    public function down()
    {
        Capsule::schema()->dropIfExists('$this->table');
    }
}
HERE;


        if (!$this->migrate_exists($file)) {
            file_put_contents(MIGRATIONS_PATH . '/' . $file, $template_table);
            echo $this->color->getColoredString('Создано', 'green') . "\n";
        } else {
            echo $this->color->getColoredString('Миграция уже существует', 'red') . "\n";
        }

    }


    public function module()
    {
        $sp = explode('/', $this->table);
        if (count($sp) == 2){
            $sp[0] = ucfirst($sp[0]);
            $sp[1] = ucfirst($sp[1]);
            $this->table = implode('/', $sp);
        }else{
            $color = new Colors();
            echo $color->getColoredString('Модуль для админки или для фронта?', 'red') . "\n";
            exit();
        }
        $module_name = MODULES . '/' . $this->table;

        if (!is_dir($module_name)) {
            mkdir($module_name, 0777);
            mkdir($module_name . '/Controllers');
            mkdir($module_name . '/Models');
            $template = <<<HERE
<?php
/*
* Необходимо зарегестрировать модуль в \Core\Settings\Base->modules
* В этих настройках можно переопределить базовые настройки,
* а так же дописать свои
*/
return [
    'routes' => [
        'GET'  => [],
        'POST' => []
    ]
];
HERE;
            file_put_contents($module_name . '/Settings.php', $template);
            $this->controller($this->table);

        } else {
            $color = new Colors();
            echo $color->getColoredString('Модуль ' . ucfirst($this->table) . ' уже существует', 'red') . "\n";
        }
    }


    public function controller($module = '')
    {
        foreach ($this->config as $item) {
            if (strpos($item, '--module=') !== false) {
                $sp = explode('=', $item);
                if (isset($sp[1])) {
                    $this->current_module = $sp[1];
                    break;
                }
            }
        }
        if ($module != ''){
            $this->current_module = $module;
        }
        $this->current_module = str_replace('/', '\\',$this->current_module);
        $this->table = str_replace('/', '\\',$this->table);

        $sp = explode('\\', $this->table);
        if (count($sp) == 2){
            $class = ucfirst($sp[1]);
        }elseif(count($sp) == 1){
            $class = ucfirst($sp[0]);
        }else{
            echo $this->color->getColoredString('Не разобрал имя класса', 'red') . "\n";
            exit();
        }

        $sp2 = explode('\\', $this->current_module);
        if (count($sp2) < 2){
            echo $this->color->getColoredString('Не понял путь к модулю', 'red') . "\n";
            exit();
        }
        $namespase = $sp2[0];
        $module_name = ucfirst($sp2[1]);

        $template = <<<HERE
<?php
namespace Modules\\$namespase\\$module_name\Controllers;


use Modules\Module;
use Extensions\Sessions\Session;


class $class extends Module
{
    /**
     * Показать список объектов.
     */
    public function index()
    {
        return view();
    }

    /**
     * Покажите форму для создания нового объекта.
     */
    public function create()
    {
        return view();
    }

    /**
     * Сохраните вновь созданный объект в хранилище.
     */
    public function store()
    {
        \$data = \$this->reguest()->getAllInputs();
        \$v = \$this->validator(\$data);
        \$v->rules([
            // Code...
        ]);
        if (\$v->validate()){
            // Code...
            set_info('-------');
            redirect(); 
        }else{
            Session::setError(\$v->getErrors());
            // Code...
            back();
        }
    }

    /**
     * Показать указанный объект.
     */
    public function show()
    {
        \$request = \$this->reguest();
        return view();
    }

    /**
     * Показать форму для редактирования указанного объекта.
     */
    public function edit()
    {
        \$request = \$this->reguest();
        return view();
    }

    /**
     * Обновите указанный объект в базе данных.
     */
    public function update()
    {
        \$request = \$this->reguest();
    }

    /**
     * Удалить указанный объект из базы данных.
     */
    public function destroy()
    {
        \$request = \$this->reguest();
    }
}
HERE;

        if (file_exists(MODULES . '/' . $namespase.'/'.$module_name. '/Controllers/' . $class . '.php')) {
            echo $this->color->getColoredString('Контроллер уже существует.', 'red') . "\n";
            return;
        }
        file_put_contents(MODULES . '/' . $namespase.'/'.$module_name. '/Controllers/' . $class . '.php', $template);
        echo $this->color->getColoredString('Создано.', 'green') . "\n";
    }


    public function model()
    {
//        print_r($this->table);
        foreach ($this->config as $item) {
            if (strpos($item, '--module=') !== false) {
                $sp = explode('=', $item);
                if (isset($sp[1])) {
                    $this->current_module = $sp[1];
                    break;
                }
            }
        }
        $c_config = isset($this->config['--module']) ? $this->config['--module'] : null;

        $class = $this->table ?? $$c_config;
        $class = ucfirst($class);
//        print_r($this->config);

        $template = <<<HERE
<?php
namespace Modules\\$this->current_module\Models;


use Core\Models\Model;

class $class extends Model
{

}
HERE;

        if ($this->current_module === false or $this->current_module === '' or $this->current_module === null) {
            echo $this->color->getColoredString('Для какого модуля?', 'red') . "\n";
            return;
        }
        if (file_exists(MODULES . '/' . $this->current_module . '/Models/' . $class . '.php')) {
            echo $this->color->getColoredString('Модель уже существует.', 'red') . "\n";
            return;
        }
        file_put_contents(MODULES . '/' . $this->current_module . '/Models/' . $class . '.php', $template);
        echo $this->color->getColoredString('Создано.', 'green') . "\n";
    }


    public function seed()
    {
        $template = <<<HERE
<?php

class $this->table
{
    public function run()
    {
        
    }
}
HERE;

        if (file_exists(SEED_PATH . '/' . $this->table . '.php')) {
            echo $this->color->getColoredString('Файл уже существует.', 'red') . "\n";
            return;
        }
        file_put_contents(SEED_PATH . '/' . $this->table . '.php', $template);
        echo $this->color->getColoredString('Создано.', 'green') . "\n";
    }
}


















