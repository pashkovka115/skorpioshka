<?php
namespace Modules\Front\Home\Controllers;


use Modules\Module;
use Extensions\Sessions\Session;


class Home extends Module
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
        $data = $this->reguest()->getAllInputs();
        $v = $this->validator($data);
        $v->rules([
            // Code...
        ]);
        if ($v->validate()){
            // Code...
            set_info('-------');
            redirect(); 
        }else{
            Session::setError($v->getErrors());
            // Code...
            back();
        }
    }

    /**
     * Показать указанный объект.
     */
    public function show()
    {
        $request = $this->reguest();
        return view();
    }

    /**
     * Показать форму для редактирования указанного объекта.
     */
    public function edit()
    {
        $request = $this->reguest();
        return view();
    }

    /**
     * Обновите указанный объект в базе данных.
     */
    public function update()
    {
        $request = $this->reguest();
    }

    /**
     * Удалить указанный объект из базы данных.
     */
    public function destroy()
    {
        $request = $this->reguest();
    }
}