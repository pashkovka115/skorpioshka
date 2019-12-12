<?php


namespace Core\Middleware;

use Core\Exceptions\CsrfException;
use Extensions\Permissions\Role;

class Admin extends Middleware
{
    public function transform($data)
    {
        if (auth()->check() and auth()->user()->hasAnyRole(Role::all())){
            return $data;
        }else{
            set_info('Не достаточно прав');
            redirect('/', 301, true);
        }
    }
}