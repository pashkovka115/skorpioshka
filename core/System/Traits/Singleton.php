<?php


namespace Core\System\Traits;


trait Singleton
{
    private static $_instance;

    /**
     * @return static
     */
    public static function getInstance(){
        if (self::$_instance instanceof self)
            return self::$_instance;
        return self::$_instance = new self();
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function toArray()
    {
        $arr = [];
        $obj = self::getInstance();
        foreach ($obj as $property => $value) {
            $arr[$property] = $value;
        }
        return $arr;
    }

}