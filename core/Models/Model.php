<?php


namespace Core\Models;


// https://cartalyst.com/manual/sentry/2.1

use Core\Router;


class Model extends \Illuminate\Database\Eloquent\Model
{
    public static function paginate($perPage = false)
    {
        $config = Router::getInstance()->getMergeSettings()['pagConfig'];
        $offset = (!empty($_GET[$config['queryStringSegment']]) ? (($_GET[$config['queryStringSegment']] - 1) * $config['perPage']) : 0) * 1;

        $limit = $perPage ? $perPage : $config['perPage'];

        return new Pagination(static::count(), static::skip($offset)->take($limit)->get(), $config);
    }

//    public function belongsToMany(){}
//        public function getConnectionName
}