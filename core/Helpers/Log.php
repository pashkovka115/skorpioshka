<?php


namespace Core\Helpers;


trait Log
{
    protected function writeLog($message, $file = 'log.txt', $event = 'Fault')
    {
        $dateTime = new \DateTime();
        $str = '[ '.$event . ' ]: ' . $dateTime->format('d-m-Y G:i:s') . ' - ' . $message . PHP_EOL;
        $dir = LOGS_PATH . '/'. date('Y') . '/' . date('m').'/' . date('d');
        if (!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        if (!file_exists($dir.'/'.$file)) file_put_contents($dir.'/'.$file, '');
        file_put_contents($dir.'/'.$file, $str, FILE_APPEND);
    }
}