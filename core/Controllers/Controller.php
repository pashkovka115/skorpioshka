<?php


namespace Core\Controllers;


use Core\Request;
use Core\Response;
use Core\Router;
use Extensions\Authorization\Loginer;
use Extensions\Authorization\Register;
use Extensions\Validation\Validator;

class Controller
{
    /**
     * @return Request
     */
    protected function reguest(){
        return Router::getInstance()->getRequest();
    }

    /**
     * @return Response
     */
    protected function response(){
        return Router::getInstance()->getResponse();
    }

    /**
     * @param array $data
     * @return Validator
     */
    protected function validator(array $data){
        return new Validator($data);
    }

    /**
     * @return bool
     * Это административная часть сайта? (не статус пользователя)
     */
    protected function isAdminDir(){
        return Router::getInstance()->isAdminDir();
    }

    /**
     * @return Loginer
     */
    protected function loginer(){
        return Loginer::getInstance();
    }

    /**
     * @return Register
     */
    protected function register(){
        return Register::getInstance();
    }
}