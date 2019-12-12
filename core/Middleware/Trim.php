<?php


namespace Core\Middleware;

class Trim extends Middleware
{
    public function transform($data)
    {
        if (is_array($data)){
            $arr = [];
            foreach ($data as $k => $datum){
                $arr[$k] = trim($datum);
            }
            return $arr;
        }
        return trim($data);
    }
}