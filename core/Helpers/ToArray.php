<?php


namespace Core\Helpers;


trait ToArray
{
    public function toArray()
    {
        $arr = [];
        foreach ($this as $property => $value) {
            $arr[$property] = $value;
        }
        return $arr;
    }
}