<?php


namespace Core\Helpers;


class Arrays
{
    public static function array_merge_recursive() {

        $arrays = func_get_args();
        $base = array_shift($arrays);
        if ($base === null) $base = [];

        foreach ($arrays as $array) {
            reset($base); //important
//            while (list($key, $value) = @each($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value) and isset($base[$key]) and is_array($base[$key])) {
                    $base[$key] = self::array_merge_recursive($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }
}