<?php


namespace Core\Console;

/**
 * Class Router
 * @package Core\Console
 * php console command:subcommand - Core\Console\Command()->subcommand()
 * php console command - Core\Console\Command()->index()
 */
class Router
{
    public $class;
    public $method;


    public function __construct($args, $count)
    {
        $sp = explode(':', $args[0]);
        $this->class = 'Core\\Console\\' . ucfirst($sp[0]);
        $this->method = isset($sp[1]) ? ucfirst($sp[1]) : 'index';

        if (class_exists($this->class)){
            $rc = new \ReflectionClass($this->class);
            if ($rc->hasMethod($this->method)){
                $obj = $rc->newInstance($args, $count);
                $method = $rc->getMethod($this->method);
                $method->invoke($obj);
            }else{
                exit("\n" . 'Нет метода: '. $this->method . "\n");
            }
        }else{
            exit("\n" . 'Нет класса: '.$this->class . "\n");
        }
    }
}