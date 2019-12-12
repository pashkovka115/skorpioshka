<?php


namespace Core\Helpers;


trait Clear
{
    /**
     * @param array|string $data
     * @return array|string
     * @throws \Exception
     */
    protected function clearData($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $key = trim(strip_tags($k));
                $val = trim(strip_tags($v));
                unset($data[$k]);
                $data[$key] = $val;
            }
            return $data;
        } elseif (is_string($data)) {
            return trim(strip_tags($data));
        } else {
            throw new \Exception('Данные не очищены!!!');
        }
    }


    protected function clearNum($data)
    {
        return $data * 1;
    }
}
















