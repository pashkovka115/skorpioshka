<?php

namespace Extensions\GenerateView;


use Core\Helpers\Arrays;
use Core\Router;
use Core\Settings\Base;
use Core\View;
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Database\Eloquent\Collection;

class GenerateView
{
    protected $inputData;
    protected $config;
    protected $table;
    protected $action;

    const NAME = 'GenerateView';
    const VIEWS = SITE_PATH . '/extensions/' . self::NAME . '/views/';
    const LAYOUTS = self::VIEWS . 'layouts/';

    /**
     * GenerateView constructor.
     * @param $data Model
     */
    public function __construct($data, string $key, string $action = 'add')
    {
        $settings = require 'Settings.php';
//        dump($settings);
        $this->action = $action;

        if (strrpos(get_class($data), 'Collection') and $data->count() == 1) {
            $data = $data[0];
        }
        if ($data instanceof \Illuminate\Database\Eloquent\Model) {
//            $table = $data->getTable();
            $this->inputData = $data->getAttributes();
            $this->config = $settings[$key];
        } elseif (strrpos(get_class($data), 'Collection')) {

            $this->inputData = $data->toArray();
//            $table = $data[0]->getTable();
            $this->config = $settings[$key];
        }
        $this->table = $key;
    }

// todo: решить вопросы: добавления, удаления. подмодули add/edit

    /**
     * @return array
     * Вызывается автоматически для склеивания настроек
     */
    /*public static function getSettings(): array
    {
        return [
            'routes'=>[
                '^'.Base::get('admin_alias').'/add/table/<table:\w+>' => 'Extensions\GenerateView\Generate@add',
                '^'.Base::get('admin_alias').'/edit/table/<table:\w+>' => 'Extensions\GenerateView\Generate@edit',
            ]
        ];
    }*/

    public function view()
    {
        $view = View::getInstance();
        $pageName = $this->config['__name'] ?? '';

//        если одна сущность
        if (is_string(array_keys($this->inputData)[0])) {

            $id = isset($this->inputData['id']) ? $this->inputData['id'] : false;

            /**
             * @var $field Имя поля в БД
             * @var $value Значение этого поля
             * @var $column Список настроек для этого поля
             */
            $templates = [];
            foreach ($this->inputData as $field => $value) {
                if (isset($this->config[$field])) {

                    $column = $this->config[$field];
                    $name = $column['name'] ?? '';
                    $description = $column['description'] ?? '';

                    $view->render(self::VIEWS . $column['type'], compact('field', 'value', 'name', 'description'));
                    $templates[] = self::VIEWS . $column['type'];
                }
            }
            $table = $this->table;
            $view->render(self::LAYOUTS . 'index', compact('templates', 'pageName', 'table'));

            return $view->getTemplate(self::LAYOUTS . 'index');
        }
        // если много сущностей
        elseif (is_int(array_keys($this->inputData)[0])) {

            $fillable = $this->config['__list'] ?? [];
            $data = $this->inputData ?? [];
            $table = $this->table;

            $view->render(self::LAYOUTS . 'list', compact('pageName', 'fillable', 'data', 'table'));
//dump($this->config);
            return $view->getTemplate(self::LAYOUTS . 'list');
        }
    }
}



























